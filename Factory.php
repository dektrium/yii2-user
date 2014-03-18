<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user;

use dektrium\user\models\UserInterface;
use yii\base\Component;

/**
 * Factory component is used to create models and forms when needed.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Factory extends Component
{
    /**
     * @var string
     */
    public $userClass = '\dektrium\user\models\User';

    /**
     * @var string
     */
    public $profileClass = '\dektrium\user\models\Profile';

    /**
     * @var string
     */
    public $userQueryClass = '\dektrium\user\models\UserQuery';

    /**
     * @var string
     */
    public $profileQueryClass = '\yii\db\ActiveQuery';

    /**
     * @var string
     */
    public $resendFormClass = '\dektrium\user\forms\Resend';

    /**
     * @var string
     */
    public $loginFormClass = '\dektrium\user\forms\Login';

    /**
     * @var string
     */
    public $passwordRecoveryFormClass = '\dektrium\user\forms\PasswordRecovery';

    /**
     * @var string
     */
    public $passwordRecoveryRequestFormClass = '\dektrium\user\forms\PasswordRecoveryRequest';

    /**
     * Creates new User model.
     *
     * @param  array             $config
     * @return UserInterface
     * @throws \RuntimeException
     */
    public function createUser($config = [])
    {
        $config['class'] = $this->userClass;
        $model = \Yii::createObject($config);
        if (!$model instanceof UserInterface) {
            throw new \RuntimeException(sprintf('"%s" must implement "%s" interface',
                get_class($model), '\dektrium\user\models\UserInterface'));
        }

        return $model;
    }

    /**
     * Creates new Profile model.
     *
     * @param  array                         $config
     * @return \dektrium\user\models\Profile
     */
    public function createProfile($config = [])
    {
        $config['class'] = $this->profileClass;

        return \Yii::createObject($config);
    }

    /**
     * Creates new query for user class.
     *
     * @return \yii\db\ActiveQuery
     */
    public function createUserQuery()
    {
        return \Yii::createObject(['class' => $this->userQueryClass, 'modelClass' => $this->userClass]);
    }

    /**
     * Creates new query for user class.
     *
     * @return \yii\db\ActiveQuery
     */
    public function createProfileQuery()
    {
        return \Yii::createObject(['class' => $this->profileQueryClass, 'modelClass' => $this->profileClass]);
    }

    /**
     * Creates new form based on its name.
     *
     * @param string $name   "registration"|"resend"|"login"|"recovery"
     * @param array  $config
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function createForm($name, $config = [])
    {
        $property = $name.'FormClass';
        if (isset($this->$property)) {
            $config['class'] = $this->$property;

            return \Yii::createObject($config);
        }

        throw new \RuntimeException("Creating unknown model: $name");
    }
}
