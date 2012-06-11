<?php

// This is the configuration for yiic console application.
return array(
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'     => 'GTTA',

    // autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
	),

	// application components
	'components' => array(
		'db' => array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=gtta',
			'username'         => 'gtta',
			'password'         => '123',
			'charset'          => 'utf8',
		),
	),

    // parameters
    'params' => array(
        'automation' => array(
            'lockFile'     => '/tmp/gtta.automation',
            'tempPath'     => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files/automation',
            'scriptsPath'  => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../scripts',
            'interpreters' => array(
                'py' => array(
                    'path'     => '/usr/bin/python',
                    'basePath' => '/usr/bin'
                ),
                'pl' => array(
                    'path'     => '/usr/bin/perl',
                    'basePath' => '/usr/bin'
                )
            )
        ),
        'cleaner' => array(
            'lockFile' => '/tmp/gtta.cleaner',
        ),
        'yiicPath' => dirname(__FILE__).'/../',
    ),
);