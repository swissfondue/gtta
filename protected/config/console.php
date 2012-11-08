<?php

$mainConfig = require(dirname(__FILE__).DIRECTORY_SEPARATOR.'main.php');

// This is the configuration for yiic console application.
return array(
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'     => 'GTTA',

    // autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
        'ext.yii-mail.YiiMailMessage',
	),

	// application components
	'components' => array(
		'db' => array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=gtta',
			'username'         => 'gtta',
			'password'         => GTTA_VIRTUAL ? '3yNeMw4sMaj6TC8gJ2Ecvh2GF' : '123',
			'charset'          => 'utf8',
		),

        'mail' => array(
            'class'         => 'ext.yii-mail.YiiMail',
            'transportType' => 'smtp',
            'viewPath'      => 'application.views.mail',
            'logging'       => false,
            'dryRun'        => false,
            'transportOptions' => GTTA_PRODUCTION ? array(
                    'host'         => 'mailbak.netprotect.ch',
                    'port'         => 25,
                    'username'     => 'web365p2',
                    'password'     => 'babuschka',
                    'encryption'   => '',
                ) :
                array(
                    'host'         => 'smtp.yandex.ru',
                    'port'         => 465,
                    'username'     => 'gtta.test@yandex.ru',
                    'password'     => '123321',
                    'encryption'   => 'ssl',
                ),
        ),

        'urlManager' => $mainConfig['components']['urlManager'],
	),

    // parameters
    'params' => array(
        // email sender
        'email' => array(
            'lockFile'    => '/tmp/gtta.email',
            'systemEmail' => GTTA_PRODUCTION ? 'gtta@netprotect.ch' : 'gtta.test@yandex.ru',
            'maxAttempts' => 3,
        ),

        // vulnerability tracker
        'vulntracker' => array(
            'lockFile' => '/tmp/gtta.vulntracker',
        ),

        // checks automation
        'automation' => array(
            'minNotificationInterval' => 5 * 60, // 5 minutes

            'lockFile'    => '/tmp/gtta.automation',
            'tempPath'    => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files/automation',
            'scriptsPath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../scripts',

            'interpreters' => array(
                'py' => array(
                    'path'     => '/usr/bin/python',
                    'basePath' => '/usr/bin'
                ),
                'pl' => array(
                    'path'     => '/usr/bin/perl',
                    'basePath' => '/usr/bin'
                ),
                'rb' => array(
                    'path'     => '/usr/bin/ruby',
                    'basePath' => '/usr/bin'
                )
            )
        ),

        // file cleaner
        'cleaner' => array(
            'lockFile' => '/tmp/gtta.cleaner',
        ),

        'yiicPath' => dirname(__FILE__).'/../',

        'timeZone' => $mainConfig['params']['timeZone'],
    ),
);