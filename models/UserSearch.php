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

use dektrium\user\helpers\FeatureHelper;
use dektrium\user\models\query\UserQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
    const SHOW_ALL_APPROVED = 1;
    const SHOW_ONLY_APPROVED = 2;
    const SHOW_ONLY_NOT_APPROVED = 3;

    const SHOW_ALL_BLOCKED = 1;
    const SHOW_ONLY_BLOCKED = 2;
    const SHOW_ONLY_NOT_BLOCKED = 3;

    const SHOW_ALL_CONFIRMED = 1;
    const SHOW_ONLY_CONFIRMED = 2;
    const SHOW_ONLY_NOT_CONFIRMED = 3;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $email;

    /**
     * @var int
     */
    public $created_at;

    /**
     * @var string
     */
    public $registration_ip;

    /**
     * @var int
     */
    public $approveStatus = self::SHOW_ALL_APPROVED;

    /**
     * @var int
     */
    public $confirmStatus = self::SHOW_ALL_CONFIRMED;

    /**
     * @var int
     */
    public $blockStatus = self::SHOW_ALL_BLOCKED;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'fieldsSafe' => [['id', 'username', 'email', 'registration_ip', 'created_at', 'filter'], 'safe'],
            'createdDefault' => ['created_at', 'default', 'value' => null],
            'approveStatus' => ['approveStatus', 'in', 'range' => array_keys($this->getApproveStatusList())],
            'confirmStatus' => ['confirmStatus', 'in', 'range' => array_keys($this->getConfirmStatusList())],
            'blockStatus' => ['blockStatus', 'in', 'range' => array_keys($this->getBlockStatusList())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'        => Yii::t('user', 'Username'),
            'email'           => Yii::t('user', 'Email'),
            'created_at'      => Yii::t('user', 'Registration time'),
            'registration_ip' => Yii::t('user', 'Registration ip'),
            'approveStatus' => Yii::t('user', 'Approve status'),
            'confirmStatus' => Yii::t('user', 'Confirm status'),
            'blockStatus' => Yii::t('user', 'Block status'),
        ];
    }

    /**
     * @param  array $params
     * @return ActiveDataProvider
     */
    public function search($params = [])
    {
        /** @var User $user */
        $user = Yii::createObject(User::className());
        $query = $user::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->created_at !== null) {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'created_at', $date, $date + 3600 * 24]);
        }

        $query
            ->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['registration_ip' => $this->registration_ip]);

        $this->applyApproveStatusCondition($query);
        $this->applyConfirmStatusCondition($query);
        $this->applyBlockStatusCondition($query);

        return $dataProvider;
    }

    /**
     * @param UserQuery $query
     */
    protected function applyApproveStatusCondition(UserQuery $query)
    {
        if (!FeatureHelper::isAdminApprovalEnabled()) {
            return;
        }

        if ($this->approveStatus == self::SHOW_ONLY_NOT_CONFIRMED) {
            $query->approved(false);
        } else if ($this->approveStatus == self::SHOW_ONLY_APPROVED) {
            $query->approved(true);
        }
    }

    /**
     * @param UserQuery $query
     */
    protected function applyConfirmStatusCondition(UserQuery $query)
    {
        if (!FeatureHelper::isEmailConfirmationEnabled()) {
            return;
        }

        if ($this->confirmStatus == self::SHOW_ONLY_NOT_CONFIRMED) {
            $query->confirmed(false);
        } else if ($this->confirmStatus == self::SHOW_ONLY_CONFIRMED) {
            $query->confirmed(true);
        }
    }

    /**
     * @param UserQuery $query
     */
    protected function applyBlockStatusCondition(UserQuery $query)
    {
        if ($this->blockStatus == self::SHOW_ONLY_NOT_BLOCKED) {
            $query->blocked(false);
        } else if ($this->blockStatus == self::SHOW_ONLY_BLOCKED) {
            $query->blocked(true);
        }
    }

    /**
     * @return array
     */
    public function getApproveStatusList()
    {
        return [
            self::SHOW_ALL_APPROVED => Yii::t('user', 'Show all'),
            self::SHOW_ONLY_APPROVED => Yii::t('user', 'Show approved'),
            self::SHOW_ONLY_NOT_APPROVED => Yii::t('user', 'Show not approved'),
        ];
    }

    /**
     * @return array
     */
    public function getConfirmStatusList()
    {
        return [
            self::SHOW_ALL_CONFIRMED => Yii::t('user', 'Show all'),
            self::SHOW_ONLY_CONFIRMED => Yii::t('user', 'Show confirmed'),
            self::SHOW_ONLY_NOT_CONFIRMED => Yii::t('user', 'Show not confirmed'),
        ];
    }

    /**
     * @return array
     */
    public function getBlockStatusList()
    {
        return [
            self::SHOW_ALL_BLOCKED => Yii::t('user', 'Show all'),
            self::SHOW_ONLY_BLOCKED => Yii::t('user', 'Show blocked'),
            self::SHOW_ONLY_NOT_BLOCKED => Yii::t('user', 'Show not blocked'),
        ];
    }
}
