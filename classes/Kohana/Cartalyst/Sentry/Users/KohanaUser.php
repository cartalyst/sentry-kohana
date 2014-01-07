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

use Cartalyst\Sentry\Users\UserInterface;
use ORM;

class KohanaUser extends ORM implements UserInterface {

	/**
	 * {@inheritDoc}
	 */
	protected $_table_name = 'users';

	/**
	 * {@inheritDoc}
	 */
	protected $_table_columns = array(
		'id'                => array('type' => 'int'),
		'email'             => array('type' => 'string'),
		'password'          => array('type' => 'string'),
		'persistence_codes' => array('type' => 'string', 'null' => true),
		'permissions'       => array('type' => 'string', 'null' => true),
		'last_login'        => array('type' => 'string', 'null' => true),
		'first_name'        => array('type' => 'string', 'null' => true),
		'last_name'         => array('type' => 'string', 'null' => true),
		'created_at'        => array('type' => 'string'),
		'updated_at'        => array('type' => 'string'),
	);

	/**
	 * {@inheritDoc}
	 */
	protected $_updated_column = array(
		'column' => 'updated_at',
		'format' => 'Y-m-d H:i:s');

	/**
	 * {@inheritDoc}
	 */
	protected $_created_column = array(
		'column' => 'created_at',
		'format' => 'Y-m-d H:i:s',
	);

	/**
	 * {@inheritDoc}
	 */
	protected $_serialize_columns = array(
		'persistence_codes',
		'permissions',
	);

	/**
	 * Array of login column names.
	 *
	 * @var array
	 */
	protected $_loginNames = array('email');

	/**
	 * Returns an array of login column names.
	 *
	 * @return array
	 */
	public function getLoginNames()
	{
		return $this->_loginNames;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserId()
	{
		return $this->id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserLogin()
	{
		$attribute = $this->getUserLoginName();

		return $this->$attribute;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserLoginName()
	{
		return reset($this->loginNames);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserPassword()
	{
		return $this->password;
	}

}
