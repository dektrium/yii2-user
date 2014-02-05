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
use yii\db\ActiveQuery;

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
	public $modelClass = '\dektrium\user\models\User';

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
	public $recoveryFormClass = '\dektrium\user\forms\Recovery';

	/**
	 * Creates new User model.
	 *
	 * @return UserInterface
	 *
	 * @throws \RuntimeException
	 */
	public function createUser()
	{
		$model = \Yii::createObject($this->modelClass);
		if (!$model instanceof UserInterface) {
			throw new \RuntimeException(sprintf('"%s" must implement "%s" interface',
				get_class($model), '\dektrium\user\models\UserInterface'));
		}

		return $model;
	}

	/**
	 * Creates new query for user class.
	 *
	 * @return ActiveQuery
	 */
	public function createQuery()
	{
		return new ActiveQuery(['modelClass' => $this->modelClass]);
	}

	/**
	 * Creates new form based on its name.
	 *
	 * @param string $name "registration"|"resend"|"login"|"recovery"
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