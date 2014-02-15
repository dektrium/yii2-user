<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var integer
	 */
	public $create_time;

	/**
	 * @var string
	 */
	public $registration_ip;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['create_time'], 'integer'],
			[['username', 'email', 'registered_from'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'username' => \Yii::t('user', 'Username'),
			'email' => \Yii::t('user', 'Email'),
			'create_time' => \Yii::t('user', 'Registration time'),
			'registered_from' => \Yii::t('user', 'Registration ip'),
		];
	}

	/**
	 * @param $params
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = User::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'username', true);
		$this->addCondition($query, 'email', true);
		$this->addCondition($query, 'create_time');
		$this->addCondition($query, 'registered_from');
		return $dataProvider;
	}

	/**
	 * @param $query
	 * @param $attribute
	 * @param bool $partialMatch
	 */
	protected function addCondition($query, $attribute, $partialMatch = false)
	{
		$value = $this->$attribute;
		if (trim($value) === '') {
			return;
		}
		if ($attribute == 'registered_from') {
			$value = ip2long($value);
		}
		if ($partialMatch) {
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}