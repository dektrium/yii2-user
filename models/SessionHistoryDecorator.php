<?php


namespace dektrium\user\models;

use dektrium\user\Finder;
use dektrium\user\helpers\SessionHelper;
use Yii;
use dektrium\user\traits\ModuleTrait;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Transaction;
use yii\web\Session;
use InvalidArgumentException;
use yii\base\InvalidArgumentException as BaseInvalidArgumentException;

/**
 * Decorator for the {@see Session} class for storing the 'session history'
 *
 * Not decorated methods:
 * {@see Session::open()}
 * {@see Session::close()}
 * {@see Session::destroy()}
 * {@see Session::get()}
 * {@see Session::set()}
 */
class SessionHistoryDecorator extends Session
{
    use ModuleTrait;

    public $sessionHistoryTable = '{{%session_history}}';

    /**
     * @var Session
     */
    public $session;

    /**
     * @var SessionHelper
     */
    public $helper;

    public function __construct(Session $session, SessionHelper $helper, $config = [])
    {
        $this->session = $session;
        $this->helper = $helper;

        parent::__construct($config);
    }

    /** @inheritdoc */
    public function init()
    {
        if (empty($this->session)) {
            throw new InvalidArgumentException('Should set $session for decoration.');
        }

        parent::init();
    }

    /** @inheritdoc */
    public function getUseCustomStorage()
    {
        return $this->session->getUseCustomStorage();
    }

    /** @inheritdoc */
    public function getIsActive()
    {
        return $this->session->getIsActive();
    }

    /** @inheritdoc */
    public function getHasSessionId()
    {
        return $this->session->getHasSessionId();
    }

    /** @inheritdoc */
    public function setHasSessionId($value)
    {
        return $this->session->setHasSessionId($value);
    }

    /** @inheritdoc */
    public function getId()
    {
        return $this->session->getId();
    }

    /** @inheritdoc */
    public function setId($value)
    {
        return $this->session->setId($value);
    }

    /** @inheritdoc */
    public function regenerateID($deleteOldSession = false)
    {
        return $this->transaction(function () use ($deleteOldSession) {
            $user = Yii::$app->user;
            $isGuest = $user->getIsGuest();
            $oldSid = session_id();
            if (false === $this->session->regenerateID($deleteOldSession)) {
                return false;
            }

            if (false === $this->getModule()->enableSessionHistory) {
                return true;
            }

            if ($isGuest) {
                $this->transaction(function () use ($oldSid) {
                    $this->unbindSessionHistory($oldSid);
                });
            } else {
                $this->transaction(function () use ($user, $oldSid) {
                     $this->getDB()->createCommand()->delete($this->sessionHistoryTable, [
                        'user_id' => $user->getId(),
                        'session_id' => $oldSid,
                    ])->execute();
                });
            }

            return true;
        });
    }

    /** @inheritdoc */
    public function getName()
    {
        return $this->session->getName();
    }

    /** @inheritdoc */
    public function setName($value)
    {
        return $this->session->setName($value);
    }

    /** @inheritdoc */
    public function getSavePath()
    {
        return $this->session->getSavePath();
    }

    /** @inheritdoc */
    public function setSavePath($value)
    {
        return $this->session->setSavePath($value);
    }

    /** @inheritdoc */
    public function getCookieParams()
    {
        return $this->session->getCookieParams();
    }

    /** @inheritdoc */
    public function setCookieParams(array $value)
    {
        return $this->session->setCookieParams($value);
    }

    /** @inheritdoc */
    public function getUseCookies()
    {
        return $this->session->getUseCookies();
    }

    /** @inheritdoc */
    public function setUseCookies($value)
    {
        return $this->session->setUseCookies($value);
    }

    /** @inheritdoc */
    public function getGCProbability()
    {
        return $this->session->getGCProbability();
    }

    /** @inheritdoc */
    public function setGCProbability($value)
    {
        return $this->session->setGCProbability($value);
    }

    /** @inheritdoc */
    public function getUseTransparentSessionID()
    {
        return $this->session->getUseTransparentSessionID();
    }

    /** @inheritdoc */
    public function setUseTransparentSessionID($value)
    {
        return $this->session->setUseTransparentSessionID($value);
    }

    /** @inheritdoc */
    public function getTimeout()
    {
        return $this->session->getTimeout();
    }

    /** @inheritdoc */
    public function setTimeout($value)
    {
        return $this->session->setTimeout($value);
    }

    /** @inheritdoc */
    public function openSession($savePath, $sessionName)
    {
        return $this->session->openSession($savePath, $sessionName);
    }

    /** @inheritdoc */
    public function closeSession()
    {
        return $this->session->closeSession();
    }

    /** @inheritdoc */
    public function readSession($id)
    {
        return $this->session->readSession($id);
    }

    /** @inheritdoc */
    public function writeSession($id, $data)
    {
        return $this->session->writeSession($id, $data) &&
            (
                false === $this->getModule()->enableSessionHistory ||
                $this->transaction(function () use ($id, $data) {
                    if (Yii::$app->user->getIsGuest()) {
                        return true;
                    }

                    $updated_at = [
                        'updated_at' => time(),
                    ];

                    /** @var SessionHistory $model */
                    $model = $this->getFinder()
                        ->findSessionHistory($this->helper->getConditionCurrentHistoryData())
                        ->one();
                    if (isset($model)) {
                        $result = $model->updateAttributes($updated_at);
                    } else {
                        Yii::createObject(SessionHistory::className());
                        $model = Yii::createObject([
                                'class' => SessionHistory::className(),
                            ] + $this->helper->getCurrentHistoryData() + $updated_at);
                        if (!$result = $model->save()) {
                            throw new BaseInvalidArgumentException(print_r($model->errors, 1));
                        }

                        $this->deleteExpiredHistory($model->user_id);
                        $this->displacementHistory($model->user_id);
                    }

                    return $result;
                })
            );
    }

    /** @inheritdoc */
    public function destroySession($id)
    {
        return $this->session->destroySession($id) &&
            (
                false === $this->getModule()->enableSessionHistory ||
                $this->transaction(function () use ($id) {
                    $this->unbindSessionHistory($id);

                    return true;
                })
            );
    }

    /** @inheritdoc */
    public function gcSession($maxLifetime)
    {
        return $this->session->gcSession($maxLifetime) &&
            (
                false === $this->getModule()->enableSessionHistory ||
                $this->transaction(function () use ($maxLifetime) {
                    $this->deleteExpiredHistory();

                    $this->getDb()->createCommand()->update(
                        $this->sessionHistoryTable,
                        ['session_id' => null],
                        $this->getConditionExpired()
                    )->execute();

                    return true;
                })
            );
    }

    /** @inheritdoc */
    public function getIterator()
    {
        return $this->session->getIterator();
    }

    /** @inheritdoc */
    public function getCount()
    {
        return $this->session->getCount();
    }

    /** @inheritdoc */
    public function count()
    {
        return $this->session->count();
    }

    /** @inheritdoc */
    public function remove($key)
    {
        return $this->session->remove($key);
    }

    /** @inheritdoc */
    public function removeAll()
    {
        return $this->session->removeAll();
    }

    /** @inheritdoc */
    public function has($key)
    {
        return $this->session->has($key);
    }

    /** @inheritdoc */
    public function getFlash($key, $defaultValue = null, $delete = false)
    {
        return $this->session->getFlash($key, $defaultValue, $delete);
    }

    /** @inheritdoc */
    public function getAllFlashes($delete = false)
    {
        return $this->session->getAllFlashes($delete);
    }

    /** @inheritdoc */
    public function setFlash($key, $value = true, $removeAfterAccess = true)
    {
        return $this->session->setFlash($key, $value, $removeAfterAccess);
    }

    /** @inheritdoc */
    public function addFlash($key, $value = true, $removeAfterAccess = true)
    {
        return $this->session->addFlash($key, $value, $removeAfterAccess);
    }

    /** @inheritdoc */
    public function removeFlash($key)
    {
        return $this->session->removeFlash($key);
    }

    /** @inheritdoc */
    public function removeAllFlashes()
    {
        return $this->session->removeAllFlashes();
    }

    /** @inheritdoc */
    public function hasFlash($key)
    {
        return $this->session->hasFlash($key);
    }

    /** @inheritdoc */
    public function offsetExists($offset)
    {
        return $this->session->offsetExists($offset);
    }

    /** @inheritdoc */
    public function offsetGet($offset)
    {
        return $this->session->offsetGet($offset);
    }

    /** @inheritdoc */
    public function offsetSet($offset, $item)
    {
        return $this->session->offsetSet($offset, $item);
    }

    /** @inheritdoc */
    public function offsetUnset($offset)
    {
        return $this->session->offsetUnset($offset);
    }

    /** @inheritdoc */
    public function setCacheLimiter($cacheLimiter)
    {
        return $this->session->setCacheLimiter($cacheLimiter);
    }

    /** @inheritdoc */
    public function getCacheLimiter()
    {
        return $this->session->getCacheLimiter();
    }

    /**
     * @param  string $id
     * @return bool
     * @throws Exception
     */
    private function unbindSessionHistory($id)
    {
        return (bool) $this->getDb()->createCommand()->update(
            $this->sessionHistoryTable,
            ['session_id' => null],
            ['session_id' => $id]
        )->execute();
    }

    /**
     *
     * @param int $userId
     * @return bool
     * @throws InvalidConfigException
     * @throws Exception
     */
    private function displacementHistory($userId)
    {
        $module = $this->getModule();

        if (false === $module->hasNumberSessionHistory()) {
            return true;
        }
        $updatedAts = $this->getFinder()
            ->findSessionHistory([
                'AND',
                ['user_id' => $userId],
                [
                    'OR',
                    $this->getConditionInactive(),
                    $this->getConditionExpired(),
                ],
            ])
            ->select(['updated_at'])
            ->limit($module->numberSessionHistory)
            ->orderBy(['updated_at' => SORT_DESC])->column();


        if (count($updatedAts) !== $module->numberSessionHistory) {
            return true;
        }

        $updatedAt = end($updatedAts);

        $condition = ['<', 'updated_at', $updatedAt];
        if ($updatedAt > $this->getTimeoutTime()) {
            $condition = [
                'OR',
                [
                    'AND',
                    $this->getConditionInactive(),
                    $condition,
                ],
                $this->getConditionExpired()
            ];
        }

        $this->getDB()->createCommand()->delete(
            $this->sessionHistoryTable,
            [
                'AND',
                ['user_id' => $userId],
                $condition,
            ]
        )->execute();

        return true;
    }

    /**
     * @param int|null $userId
     * @return bool
     * @throws Exception
     */
    private function deleteExpiredHistory($userId = null)
    {
        $result = true;
        if ($this->getModule()->hasTimeoutSessionHistory()) {
            $deleteWhere = [
                'AND',
            ];

            if (isset($userId)) {
                $deleteWhere[] = ['user_id' => $userId];
            }

            $deleteWhere[] = $this->getConditionExpired();

            $result = (bool)$this->getDB()->createCommand()->delete(
                $this->sessionHistoryTable,
                $deleteWhere
            )->execute();
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getConditionInactive()
    {
        return ['session_id' => null];
    }

    /**
     * @return array
     */
    private function getConditionExpired()
    {
        return ['<', 'updated_at', $this->getTimeoutTime()];
    }

    /**
     * @return int
     */
    private function getTimeoutTime()
    {
        $module = $this->getModule();
        $time = time() - max($module->rememberFor, $this->getTimeout());
        if (false === $module->hasTimeoutSessionHistory()) {
            return $time;
        }

        return $time - $module->timeoutSessionHistory;
    }

    /**
     * @param callable $callback
     * @return bool
     */
    private function transaction(callable $callback)
    {
        $transaction = $this->getDb()->beginTransaction();

        try {
            call_user_func($callback, $transaction);
            if ($transaction->isActive) {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            $this->rollbackTransaction($transaction, $e);
        } catch (\Throwable $e) {
            $this->rollbackTransaction($transaction, $e);
        }

        return true;
    }

    /**
     * @param Transaction $transaction
     * @param \Exception|\Throwable $e
     */
    private function rollbackTransaction($transaction, $e)
    {
        try {
            $transaction->rollBack();

            Yii::error($e);
        } catch (\Exception $e) {
            Yii::error($e, __METHOD__);
        }
    }

    /**
     * @return Finder
     * @throws InvalidConfigException
     */
    protected function getFinder()
    {
        return Yii::$container->get(Finder::className());
    }
}