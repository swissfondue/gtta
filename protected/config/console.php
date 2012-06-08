<?php

// This is the configuration for yiic console application.
return array(
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'     => 'GTTA',

	// application components
	'components'=>array(
		'db' => array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=gtta',
			'username'         => 'gtta',
			'password'         => '123',
			'charset'          => 'utf8',
		),
	),
);