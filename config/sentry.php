<?php

// Override parent configuration where required
$config = require_once __DIR__.'/../../../vendor/cartalyst/sentry/src/config/config.php';

return array_replace_recursive($config, array(

	'users' => array(
		'model' => 'Cartalyst\Sentry\Users\KohanaUser',
	),

));
