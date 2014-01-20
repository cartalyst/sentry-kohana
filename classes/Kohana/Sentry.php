<?php namespace Kohana;
/**
 * Part of the Sentry package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Cookies\KohanaCookie;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Persistence\SentryPersistence;
use Cartalyst\Sentry\Sentry as BaseSentry;
use Cartalyst\Sentry\Sessions\KohanaSession;
use Cartalyst\Sentry\Users\KohanaUserRepository;
use Kohana;
use Session;

class Sentry {

	/**
	 * Cached Sentry instance.
	 *
	 * @var \Cartalyst\Sentry\Sentry
	 */
	protected static $instance;

	/**
	 * Retrieve the cached instance of Sentry.
	 *
	 * @return \Cartalyst\Sentry\Sentry
	 */
	public static function instance()
	{
		if (static::$instance === null)
		{
			static::$instance = static::factory();
		}

		return static::$instance;
	}

	/**
	 * Creates a new instance of Sentry.
	 *
	 * @return \Cartalyst\Sentry\Sentry
	 */
	public static function factory()
	{
		$persistence = static::createPersistence();
		$users = static::createUserRepository();

		return new BaseSentry(
			$persistence,
			$users
		);
	}

	/**
	 * Create a persistence instance.
	 *
	 * @return \Cartalyst\Sentry\Persistence\SentryPersistence
	 */
	protected static function createPersistence()
	{
		$session = new KohanaSession(Session::instance());
		$cookie  = new KohanaCookie;

		return new SentryPersistence($session, $cookie);
	}

	/**
	 * Create a user repository.
	 *
	 * @return \Cartalyst\Sentry\Users\KohanaUserRepository
	 */
	protected static function createUserRepository()
	{
		$hasher = new NativeHasher;
		$model = Kohana::$config->load('sentry')->users['model'];

		return new KohanaUserRepository($hasher, $model);
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		$instance = static::instance();

		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}

}
