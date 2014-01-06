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

use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Users\UserRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;

class KohanaUserRepository implements UserRepositoryInterface {

	/**
	 * Hasher.
	 *
	 * @var \Cartalyst\Sentry\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Users\KohanaUser';

	/**
	 * Create a new Illuminate user repository.
	 *
	 * @param  \Cartalyst\Sentry\Hashing\HasherInterface  $hasher
	 * @param  string  $model
	 * @return void
	 */
	public function __construct(HasherInterface $hasher, $model = null)
	{
		$this->hasher = $hasher;

		if (isset($model))
		{
			$this->model = $model;
		}
	}

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
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		return $this->hasher->check($credentials['password'], $user->password);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByPersistenceCode($code)
	{
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function recordLogin(UserInterface $user)
	{
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function recordLogout(UserInterface $user)
	{
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $credentials)
	{
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($user, array $credentials)
	{
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $credentials, Closure $callback = null)
	{
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($user, array $credentials)
	{
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * Parses the given credentials to return logins, password and others.
	 *
	 * @param  array  $credentials
	 * @param  array  $loginNames
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	protected function parseCredentials(array $credentials, array $loginNames)
	{
		if (isset($credentials['password']))
		{
			$password = $credentials['password'];
			unset($credentials['password']);
		}
		else
		{
			$password = null;
		}

		$passedNames = array_intersect_key($credentials, array_flip($loginNames));

		if (count($passedNames) > 0)
		{
			$logins = array();

			foreach ($passedNames as $name => $value)
			{
				$logins[$name] = $credentials[$name];
				unset($credentials[$name]);
			}
		}
		elseif (isset($credentials['login']))
		{
			$logins = $credentials['login'];
			unset($credentials['login']);
		}
		else
		{
			$logins = array();
		}

		return array($logins, $password, $credentials);
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
		throw new \BadMethodCallException('Method not implemented yet.');
	}

	/**
	 * Validates the user.
	 *
	 * @param  array  $credentials
	 * @param  int  $id
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	protected function validateUser(array $credentials, $id = null)
	{
		$instance = $this->createModel();
		$loginNames = $instance->getLoginNames();

		// We will simply parse credentials which checks logins and passwords
		list($logins, $password, $credentials) = $this->parseCredentials($credentials, $loginNames);

		if ($id === null)
		{
			if (empty($logins))
			{
				throw new \InvalidArgumentException('No [login] credential was passed.');
			}
			if ($password === null)
			{
				throw new \InvalidArgumentException('You have not passed a [password].');
			}
		}

		if ($password and strlen($password) < 6)
		{
			throw new \InvalidArgumentException('Your [password] must be at least 6 characters.');
		}

		return true;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

	/**
	 * Runtime override of the model.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function setModel($model)
	{
		$this->model = $model;
	}

}
