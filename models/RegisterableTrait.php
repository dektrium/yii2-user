<?php namespace dektrium\user\models;

/**
 * RegisterableTrait is responsible for registering user accounts.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait RegisterableTrait
{
	/**
	 * Registers a user.
	 *
	 * @param  bool $generatePassword Whether to generate password for user automatically.
	 * @return bool
	 * @throws \RuntimeException
	 */
	public function register($generatePassword = false)
	{
		if (!$this->isNewRecord) {
			throw new \RuntimeException('Calling "'.__CLASS__.'::register()" on existing user');
		}

		if ($generatePassword) {
			$password = $this->generatePassword(8);
			$this->setAttribute('password', $password);
			// TODO: send welcome message
		}

		$this->trigger('beforeRegister');

		if ($this->save()) {
			$this->trigger('afterRegister');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
	 * digit. The remaining characters in the password are chosen at random from those four sets.
	 * @see https://gist.github.com/tylerhall/521810
	 * @param $length
	 * @return string
	 */
	protected function generatePassword($length)
	{
		$sets = [
			'abcdefghjkmnpqrstuvwxyz',
			'ABCDEFGHJKMNPQRSTUVWXYZ',
			'23456789'
		];
		$all = '';
		$password = '';
		foreach($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for ($i = 0; $i < $length - count($sets); $i++) {
			$password .= $all[array_rand($all)];
		}

		$password = str_shuffle($password);

		return $password;
	}

	/**
	 * @return \dektrium\user\Module
	 */
	abstract protected function getModule();
}