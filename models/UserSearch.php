<?php namespace dektrium\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
	public $id;
	public $username;
	public $email;
	public $create_time;
	public $update_time;
	public $registration_ip;
	public $login_ip;
	public $login_time;
	public $confirmation_time;

	public function rules()
	{
		return [
			[['id', 'create_time', 'update_time', 'registration_ip', 'login_ip', 'login_time', 'confirmation_time'], 'integer'],
			[['username', 'email'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'username' => 'Username',
			'email' => 'Email',
			'password_hash' => 'Password Hash',
			'auth_key' => 'Auth Key',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'registration_ip' => 'Registration Ip',
			'login_ip' => 'Login Ip',
			'login_time' => 'Login Time',
			'confirmation_token' => 'Confirmation Token',
			'confirmation_time' => 'Confirmation Time',
			'confirmation_sent_time' => 'Confirmation Sent Time',
			'recovery_token' => 'Recovery Token',
			'recovery_sent_time' => 'Recovery Sent Time',
		];
	}

	public function search($params)
	{
		$query = User::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'id');
		$this->addCondition($query, 'username', true);
		$this->addCondition($query, 'email', true);
		$this->addCondition($query, 'create_time');
		$this->addCondition($query, 'update_time');
		$this->addCondition($query, 'registration_ip');
		$this->addCondition($query, 'login_ip');
		$this->addCondition($query, 'login_time');
		$this->addCondition($query, 'confirmation_time');
		return $dataProvider;
	}

	protected function addCondition($query, $attribute, $partialMatch = false)
	{
		$value = $this->$attribute;
		if (trim($value) === '') {
			return;
		}
		if ($partialMatch) {
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}