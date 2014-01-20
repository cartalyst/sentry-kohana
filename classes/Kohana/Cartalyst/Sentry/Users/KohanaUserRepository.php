<?php namespace Kohana\Cartalyst\Sentry\Users;
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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Users\BaseUserRepository;
use Cartalyst\Sentry\Users\UserRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;

class KohanaUserRepository extends BaseUserRepository implements UserRepositoryInterface {

	/**
	 * {@inheritDoc}
	 */
	protected $model = 'Cartalyst\Sentry\Users\KohanaUser';

	/**
	 * {@inheritDoc}
	 */
	public function findById($id)
	{
		$user = $this
			->createModel()
			->where('id', '=', $id)
			->find();

		return $user->loaded() ? $user : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByCredentials(array $credentials)
	{
		$instance = $this->createModel();
		$loginNames = $instance->getLoginNames();

		list($logins, $password, $credentials) = $this->parseCredentials($credentials, $loginNames);

		if (is_array($logins))
		{
			foreach ($logins as $key => $value)
			{
				$instance->where($key, '=', $value);
			}
		}
		else
		{
			$instance->and_where_open();

			foreach ($loginNames as $name)
			{
				$instance->or_where($name, '=', $logins);
			}

			$instance->and_where_close();
		}

		$user = $instance->find();

		return $user->loaded() ? $user : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByPersistenceCode($code)
	{
		// Narrow down our query to those who's persistence codes array
		// contains ours. We'll filter the right user out.
		$users = $this->createModel()
			->where('persistence_codes', 'like', "%{$code}%")
			->find_all()
			->as_array();

		$users = array_filter($users, function($user) use ($code)
		{
			$persistenceCodes = $user->persistence_codes;

			return is_array($persistenceCodes) && in_array($code, $persistenceCodes);
		});

		if (count($users) > 1)
		{
			throw new \RuntimeException('Multiple users were found with the same persistence code. This should not happen.');
		}

		return reset($users);
	}

	/**
	 * {@inheritDoc}
	 */
	public function recordLogin(UserInterface $user)
	{
		$user->last_login = date('Y-m-d H:i:s');
		return $user->save();
	}

	/**
	 * Fills a user with the given credentials, intelligently.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  array  $credentials
	 * @return void
	 */
	protected function fill(UserInterface $user, array $credentials)
	{
		$loginNames = $user->getLoginNames();

		list($logins, $password, $credentials) = $this->parseCredentials($credentials, $loginNames);

		if (is_array($logins))
		{
			foreach ($logins as $key => $value)
			{
				$user->$key = $value;
			}
		}
		else
		{
			$loginName = reset($loginNames);
			$user->$loginName = $logins;
		}

		foreach ($credentials as $key => $value)
		{
			$user->$key = $value;
		}

		if (isset($password))
		{
			$password = $this->hasher->hash($password);
			$user->password = $password;
		}
	}

}
