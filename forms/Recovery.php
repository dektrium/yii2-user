<?php namespace dektrium\user\forms;

use dektrium\user\models\User;
use yii\base\Model;
use yii\db\ActiveQuery;

class Recovery extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var User
     */
    protected $identity;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'request' => ['email'],
            'reset'   => ['password']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required', 'on' => 'request'],
            ['email', 'email', 'on' => 'request'],
            ['email', 'exist', 'className' => '\dektrium\user\models\User', 'on' => 'request'],
            ['password', 'required', 'on' => 'reset'],
            ['password', 'string', 'min' => 6, 'on' => 'reset']
        ];
    }

    /**
     * Validates form and sends recovery message to user.
     *
     * @return bool
     */
    public function sendRecoveryMessage()
    {
        if ($this->validate() && $this->scenario == 'request') {
            $query = new ActiveQuery(['modelClass' => \Yii::$app->getUser()->identityClass]);
            /** @var \dektrium\user\models\User $user */
            $user = $query->where(['email' => $this->email])->one();
            $user->sendRecoveryMessage();
            \Yii::$app->getSession()->setFlash('recovery_message_sent');
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'recovery-form';
    }

    /**
     * Resets user's password.
     *
     * @return bool
     */
    public function reset()
    {
        if ($this->validate()) {
            $this->identity->scenario = 'reset';
            $this->identity->password = $this->password;
            $this->identity->recovery_token = null;
            $this->identity->recovery_sent_time = null;
            if ($this->identity->save()) {
                \Yii::$app->getSession()->setFlash('password_reset');
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     */
    public function setIdentity(User $user)
    {
        $this->identity = $user;
    }
}