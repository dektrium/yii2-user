<?php

/*
 * This file is part of the DDMTechDev project.
 *
 * (c) DDMTechDev project <http://github.com/ddmtechdev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ddmtechdev\user\controllers;

use ddmtechdev\user\Finder;
use ddmtechdev\user\models\Profile;
use ddmtechdev\user\models\SettingsForm;
use ddmtechdev\user\models\User;
use ddmtechdev\user\Module;
use ddmtechdev\user\traits\AjaxValidationTrait;
use ddmtechdev\user\traits\EventTrait;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 *
 * @property \ddmtechdev\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before updating user's profile.
     * Triggered with \ddmtechdev\user\events\UserEvent.
     */
    const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';

    /**
     * Event is triggered after updating user's profile.
     * Triggered with \ddmtechdev\user\events\UserEvent.
     */
    const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';

    /**
     * Event is triggered before updating user's account settings.
     * Triggered with \ddmtechdev\user\events\FormEvent.
     */
    const EVENT_BEFORE_ACCOUNT_UPDATE = 'beforeAccountUpdate';

    /**
     * Event is triggered after updating user's account settings.
     * Triggered with \ddmtechdev\user\events\FormEvent.
     */
    const EVENT_AFTER_ACCOUNT_UPDATE = 'afterAccountUpdate';

    /**
     * Event is triggered before changing users' email address.
     * Triggered with \ddmtechdev\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

    /**
     * Event is triggered after changing users' email address.
     * Triggered with \ddmtechdev\user\events\UserEvent.
     */
    const EVENT_AFTER_CONFIRM = 'afterConfirm';

    /**
     * Event is triggered before disconnecting social account from user.
     * Triggered with \ddmtechdev\user\events\ConnectEvent.
     */
    const EVENT_BEFORE_DISCONNECT = 'beforeDisconnect';

    /**
     * Event is triggered after disconnecting social account from user.
     * Triggered with \ddmtechdev\user\events\ConnectEvent.
     */
    const EVENT_AFTER_DISCONNECT = 'afterDisconnect';

    /**
     * Event is triggered before deleting user's account.
     * Triggered with \ddmtechdev\user\events\UserEvent.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * Event is triggered after deleting user's account.
     * Triggered with \ddmtechdev\user\events\UserEvent.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    /** @inheritdoc */
    public $defaultAction = 'profile';

    /** @var Finder */
    protected $finder;

    /**
     * @param string           $id
     * @param \yii\base\Module $module
     * @param Finder           $finder
     * @param array            $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post'],
                    'delete'     => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['profile', 'account', 'networks', 'disconnect', 'delete'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['confirm'],
                        'roles'   => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Shows profile settings form.
     *
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        $model = $this->finder->findProfileById(\Yii::$app->user->identity->getId());

        if ($model == null) {
            $model = \Yii::createObject(Profile::className());
            $model->link('user', \Yii::$app->user->identity);
        }

        $event = $this->getProfileEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Your profile has been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    /**
     * Displays page where user can update account settings (username, email or password).
     *
     * @return string|\yii\web\Response
     */
    public function actionAccount()
    {
        /** @var SettingsForm $model */
        $model = \Yii::createObject(SettingsForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account details have been updated'));
            $this->trigger(self::EVENT_AFTER_ACCOUNT_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('account', [
            'model' => $model,
        ]);
    }

    /**
     * Attempts changing user's email address.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null || $this->module->emailChangeStrategy == Module::STRATEGY_INSECURE) {
            throw new NotFoundHttpException();
        }

        $event = $this->getUserEvent($user);

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);
        $user->attemptEmailChange($code);
        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

        return $this->redirect(['account']);
    }

    /**
     * Displays list of connected network accounts.
     *
     * @return string
     */
    public function actionNetworks()
    {
        return $this->render('networks', [
            'user' => \Yii::$app->user->identity,
        ]);
    }

    /**
     * Disconnects a network account from user.
     *
     * @param int $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDisconnect($id)
    {
        $account = $this->finder->findAccount()->byId($id)->one();

        if ($account === null) {
            throw new NotFoundHttpException();
        }
        if ($account->user_id != \Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $event = $this->getConnectEvent($account, $account->user);

        $this->trigger(self::EVENT_BEFORE_DISCONNECT, $event);
        $account->delete();
        $this->trigger(self::EVENT_AFTER_DISCONNECT, $event);

        return $this->redirect(['networks']);
    }

    /**
     * Completely deletes user's account.
     *
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionDelete()
    {
        if (!$this->module->enableAccountDelete) {
            throw new NotFoundHttpException(\Yii::t('user', 'Not found'));
        }

        /** @var User $user */
        $user  = \Yii::$app->user->identity;
        $event = $this->getUserEvent($user);

        \Yii::$app->user->logout();

        $this->trigger(self::EVENT_BEFORE_DELETE, $event);
        $user->delete();
        $this->trigger(self::EVENT_AFTER_DELETE, $event);

        \Yii::$app->session->setFlash('info', \Yii::t('user', 'Your account has been completely deleted'));

        return $this->goHome();
    }
}
