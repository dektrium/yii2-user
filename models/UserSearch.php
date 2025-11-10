<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\models;

use AlexeiKaDev\Yii2User\Finder;
// Import User for table name
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
    /** @var int|null */
    public $id = null;

    /** @var string|null */
    public $username = null;

    /** @var string|null */
    public $email = null;

    /** @var int|string|null Date range or timestamp */
    public $created_at = null;

    /** @var int|string|null Date range or timestamp */
    public $last_login_at = null;

    /** @var string|null */
    public $registration_ip = null;

    /** @var Finder The finder instance. */
    protected $finder;

    /**
     * @param Finder $finder The finder instance.
     * @param array  $config Name-value pairs that will be used to initialize the object properties.
     */
    public function __construct($finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            'fieldsSafe' => [['id', 'username', 'email', 'registration_ip', 'created_at', 'last_login_at'], 'safe'],
            // Setting default to null might not be necessary if properties are already nullable
            // 'createdDefault' => ['created_at', 'default', 'value' => null],
            // 'lastloginDefault' => ['last_login_at', 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', '#'),
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'created_at' => Yii::t('user', 'Registration time'),
            'last_login_at' => Yii::t('user', 'Last login'),
            'registration_ip' => Yii::t('user', 'Registration ip'),
        ];
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array<string, mixed> $params The data array. This is usually `Yii::$app->request->queryParams`.
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->finder->getUserQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        /** @var class-string<User> $modelClass */
        $modelClass = $query->modelClass;
        $tableName = $modelClass::tableName();

        // Apply date range filter for created_at if it's a string (potentially from a date range picker)
        if (is_string($this->created_at) && !empty($this->created_at)) {
            // Example assumes 'YYYY-MM-DD - YYYY-MM-DD' format or similar
            // Needs specific parsing logic based on the expected date format
            $dateParts = explode(' - ', $this->created_at);

            if (count($dateParts) === 2) {
                $startDate = strtotime($dateParts[0] . ' 00:00:00');
                $endDate = strtotime($dateParts[1] . ' 23:59:59');

                if ($startDate && $endDate) {
                    $query->andFilterWhere(['between', $tableName . '.created_at', $startDate, $endDate]);
                }
            } else { // Handle single date
                $date = strtotime($this->created_at);

                if ($date) {
                    $query->andFilterWhere(['between', $tableName . '.created_at', $date, $date + 86399]); // 24 hours - 1 second
                }
            }
        } elseif (is_numeric($this->created_at)) { // Handle timestamp if provided directly
            $query->andFilterWhere([$tableName . '.created_at' => $this->created_at]);
        }

        // Similar logic could be applied for last_login_at if needed

        $query->andFilterWhere(['like', $tableName . '.username', $this->username])
              ->andFilterWhere(['like', $tableName . '.email', $this->email])
              ->andFilterWhere([$tableName . '.id' => $this->id])
              ->andFilterWhere(['like', $tableName . '.registration_ip', $this->registration_ip]); // Use like for IP search

        return $dataProvider;
    }
}
