<?php namespace dektrium\user\models;

/**
 * Interface RegisterableInterface
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
interface RegisterableInterface
{
	/**
	 * Registers a user.
	 *
	 * @param  bool $generatePassword Whether to generate password for user automatically.
	 * @return bool
	 */
	public function register($generatePassword = false);
}