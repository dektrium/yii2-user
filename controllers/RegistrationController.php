<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\controllers;

use dektrium\user\Finder;
use dektrium\user\models\RegistrationForm;
use dektrium\user\models\ResendForm;
use dektrium\user\models\User;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationController extends Controller
{
    /** @var User */
    protected $user;

    /** @var RegistrationForm */
    protected $registrationForm;

    /** @var ResendForm */
    protected $resendForm;

    /** @var Finder */
    protected $finder;

    /**
     * @param string           $id
     * @param \yii\base\Module $module
     * @param RegistrationForm $regForm
     * @param ResendForm       $resendForm
     * @param User             $user
     * @param Finder           $finder
     * @param array            $config
     */
    public function __construct($id, $module, RegistrationForm $regForm, User $user, ResendForm $resendForm, Finder $finder, $config = [])
    {
        $this->user             = $user;
        $this->registrationForm = $regForm;
        $this->resendForm       = $resendForm;
        $this->finder           = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['register', 'connect'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['confirm', 'resend'], 'roles' => ['?', '@']],
                ]
            ],
        ];
    }

    /**
     * Displays the registration page.
     * After successful registration if enableConfirmation is enabled shows info message otherwise redirects to home page.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionRegister()
    {
        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException;
        }

        if ($this->registrationForm->load(\Yii::$app->request->post()) && $this->registrationForm->register()) {
            return $this->render('finish');
        }

        return $this->render('register', [
            'model' => $this->registrationForm
        ]);
    }

    /**
     * Displays page where user can create new account that will be connected to social account.
     *
     * @param  integer $account_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionConnect($account_id)
    {
        $account = $this->finder->findAccountById($account_id);

        if ($account === null || $account->getIsConnected()) {
            throw new NotFoundHttpException;
        }

        $this->user->scenario = 'connect';
        if ($this->user->load(\Yii::$app->request->post()) && $this->user->create()) {
            $account->user_id = $this->user->id;
            $account->save(false);
            \Yii::$app->user->login($this->user, $this->module->rememberFor);
            return $this->goBack();
        }

        return $this->render('connect', [
            'model'   => $this->user,
            'account' => $account
        ]);
    }

    /**
     * Confirms user's account. If confirmation was successful logs the user and shows success message. Otherwise
     * shows error message.
     *
     * @param  integer $id
     * @param  string  $code
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null || $this->module->enableConfirmation == false) {
            throw new NotFoundHttpException;
        }

        if ($user->attemptConfirmation($code)) {
            \Yii::$app->user->login($user);
            \Yii::$app->session->setFlash('user.confirmation_finished');
        } else {
            \Yii::$app->session->setFlash('user.invalid_token');
        }

        return $this->render('finish');
    }

    /**
     * Displays page where user can request new confirmation token. If resending was successful, displays message.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionResend()
    {
        if ($this->module->enableConfirmation == false) {
            throw new NotFoundHttpException;
        }

        if ($this->resendForm->load(\Yii::$app->request->post()) && $this->resendForm->resend()) {
            return $this->render('finish');
        }

        return $this->render('resend', [
            'model' => $this->resendForm
        ]);
    }
}
