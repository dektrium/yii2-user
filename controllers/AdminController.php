<?php

declare(strict_types=1);

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\controllers;

use AlexeiKaDev\Yii2User\filters\AccessRule;
use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\helpers\Password;
use AlexeiKaDev\Yii2User\models\Profile;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\models\UserSearch;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\services\UserCreationService;
use AlexeiKaDev\Yii2User\traits\EventTrait;
use Yii;
use yii\base\ExitException;
use yii\base\Model;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * AdminController allows you to administrate users.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AdminController extends Controller
{
    use EventTrait;

    /**
     * Event is triggered before creating new user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_CREATE = 'beforeCreate';

    /**
     * Event is triggered after creating new user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_CREATE = 'afterCreate';

    /**
     * Event is triggered before updating existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    /**
     * Event is triggered after updating existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_UPDATE = 'afterUpdate';

    /**
     * Event is triggered before impersonating as another user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_IMPERSONATE = 'beforeImpersonate';

    /**
     * Event is triggered after impersonating as another user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_IMPERSONATE = 'afterImpersonate';

    /**
     * Event is triggered before updating existing user's profile.
     * Triggered with \AlexeiKaDev\Yii2User\events\ProfileEvent.
     */
    public const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';

    /**
     * Event is triggered after updating existing user's profile.
     * Triggered with \AlexeiKaDev\Yii2User\events\ProfileEvent.
     */
    public const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';

    /**
     * Event is triggered before confirming existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

    /**
     * Event is triggered after confirming existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_CONFIRM = 'afterConfirm';

    /**
     * Event is triggered before deleting existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * Event is triggered after deleting existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_DELETE = 'afterDelete';

    /**
     * Event is triggered before blocking existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_BLOCK = 'beforeBlock';

    /**
     * Event is triggered after blocking existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_BLOCK = 'afterBlock';

    /**
     * Event is triggered before unblocking existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_UNBLOCK = 'beforeUnblock';

    /**
     * Event is triggered after unblocking existing user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_UNBLOCK = 'afterUnblock';

    /**
     * Name of the session key in which the original user id is saved
     * when using the impersonate user function.
     * Used inside actionSwitch().
     */
    public const ORIGINAL_USER_SESSION_KEY = 'original_user_id';

    /** @var Finder */
    protected Finder $finder;
    protected UserCreationService $userCreationService;

    /**
     * @param string $id
     * @param BaseModule $module
     * @param Finder $finder
     * @param UserCreationService $userCreationService
     * @param array $config
     */
    public function __construct(
        string $id,
        BaseModule $module,
        Finder $finder,
        UserCreationService $userCreationService,
        array $config = []
    ) {
        $this->finder = $finder;
        $this->userCreationService = $userCreationService;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'resend-password' => ['post'],
                    'block' => ['post'],
                    'switch' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'ruleConfig' => [
                    'class' => AccessRule::class,
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['switch'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): string
    {
        Url::remember('', 'actions-redirect');
        /** @var UserSearch $searchModel */
        $searchModel = Yii::createObject(UserSearch::class);
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     *
     * @return string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var User $user */
        $user = Yii::createObject([
            'class' => User::class,
            'scenario' => 'create',
        ]);
        $event = $this->getUserEvent($user);

        $this->performAjaxValidation($user);

        $this->trigger(self::EVENT_BEFORE_CREATE, $event);

        if ($user->load(Yii::$app->request->post())) {
            if ($user->validate()) {
                if ($this->userCreationService->create($user)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));
                    $this->trigger(self::EVENT_AFTER_CREATE, $event);
                    return $this->redirect(['update', 'id' => $user->id]);
                } else {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'User could not be created. Please try again.'));
                }
            } else {
                Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'Please correct errors below.'));
            }
        }

        return $this->render('create', [
            'user' => $user,
        ]);
    }

    /**
     * Updates an existing User model.
     *
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $user->scenario = 'update';
        $event = $this->getUserEvent($user);

        $this->performAjaxValidation($user);

        $this->trigger(self::EVENT_BEFORE_UPDATE, $event);

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Account details have been updated'));
            $this->trigger(self::EVENT_AFTER_UPDATE, $event);

            return $this->refresh();
        }

        return $this->render('_account', [
            'user' => $user,
        ]);
    }

    /**
     * Updates an existing profile.
     *
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateProfile(int $id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $profile = $user->profile;

        if ($profile === null) {
            $profile = Yii::createObject(Profile::class);
            $profile->link('user', $user);
        }
        $event = $this->getProfileEvent($profile);

        $this->performAjaxValidation($profile);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);

        if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Profile details have been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);

            return $this->refresh();
        }

        return $this->render('_profile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Shows information about user.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInfo(int $id): string
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        return $this->render('_info', [
            'user' => $user,
        ]);
    }

    /**
     * Switches to the given user for impersonation.
     * Requires applicable configuration settings enableImpersonateUser.
     *
     * @param int $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionSwitch(int $id): Response
    {
        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('user');

        if (!$userModule->enableImpersonateUser) {
            throw new ForbiddenHttpException(Yii::t('user', 'Impersonate user is disabled'));
        }

        if (($originalUserId = Yii::$app->session->get(self::ORIGINAL_USER_SESSION_KEY)) === null) {
            $originalUserId = Yii::$app->user->id;
            Yii::$app->session->set(self::ORIGINAL_USER_SESSION_KEY, $originalUserId);
        } elseif ($id == $originalUserId) {
            $id = $originalUserId;
            Yii::$app->session->remove(self::ORIGINAL_USER_SESSION_KEY);
        }

        $user = $this->findModel($id);
        $event = $this->getUserEvent($user);
        $this->trigger(self::EVENT_BEFORE_IMPERSONATE, $event);

        Yii::$app->user->switchIdentity($user, $userModule->rememberFor);
        Yii::$app->session->setFlash('success', Yii::t('user', 'Switched identities successfully'));
        $this->trigger(self::EVENT_AFTER_IMPERSONATE, $event);

        return $this->goHome();
    }

    /**
     * If an administrator travels back from the impersonated account, he will be logged in as his original self.
     * Requires applicable configuration settings enableImpersonateUser.
     *
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionSwitchBack(): Response
    {
        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('user');

        if (!$userModule->enableImpersonateUser) {
            throw new ForbiddenHttpException(Yii::t('user', 'Impersonate user is disabled'));
        }

        $originalUserId = Yii::$app->session->get(self::ORIGINAL_USER_SESSION_KEY);

        if ($originalUserId === null) {
            throw new ForbiddenHttpException(Yii::t('user', 'Not currently impersonating anyone.'));
        }

        $user = $this->findModel($originalUserId);
        Yii::$app->session->remove(self::ORIGINAL_USER_SESSION_KEY);
        Yii::$app->user->switchIdentity($user);
        Yii::$app->session->setFlash('success', Yii::t('user', 'Returned to original identity'));

        return $this->goHome();
    }

    /**
     * Displays the assignments page for the user.
     * Requires https://github.com/yii2-developer/yii2-rbac installation.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAssignments(int $id): string
    {
        if (!isset(Yii::$app->extensions['yii2-developer/yii2-rbac'])) {
            throw new NotFoundHttpException('yii2-rbac extension is not installed');
        }
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        return $this->render('_assignments', [
            'user' => $user,
        ]);
    }

    /**
     * Confirms the User.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionConfirm(int $id): Response
    {
        $model = $this->findModel($id);
        $event = $this->getUserEvent($model);

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);
        $model->confirm();
        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been confirmed'));

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not remove your own account'));
        } else {
            $model = $this->findModel($id);
            $event = $this->getUserEvent($model);
            $this->trigger(self::EVENT_BEFORE_DELETE, $event);
            $model->delete();
            $this->trigger(self::EVENT_AFTER_DELETE, $event);
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been deleted'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Blocks the user.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionBlock(int $id): Response
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not block your own account'));
        } else {
            $user = $this->findModel($id);
            $event = $this->getUserEvent($user);

            if ($user->getIsBlocked()) {
                $this->trigger(self::EVENT_BEFORE_UNBLOCK, $event);
                $user->unblock();
                $this->trigger(self::EVENT_AFTER_UNBLOCK, $event);
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been unblocked'));
            } else {
                $this->trigger(self::EVENT_BEFORE_BLOCK, $event);
                $user->block();
                $this->trigger(self::EVENT_AFTER_BLOCK, $event);
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been blocked'));
            }
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Generates a new password and sends it to the user.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionResendPassword(int $id): Response
    {
        $user = $this->findModel($id);

        if ($user->resendPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('user', 'New Password has been sent'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'Error while trying to send new password'));
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): User
    {
        $user = $this->finder->findUserById($id);

        if ($user === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $user;
    }

    /**
     * Performs AJAX validation.
     *
     * @param Model|Model[] $model The model(s) to be validated
     * @throws ExitException
     */
    protected function performAjaxValidation($model): void
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = ActiveForm::validate($model);
                Yii::$app->response->send();
                Yii::$app->end();
            }
        }
    }
}
