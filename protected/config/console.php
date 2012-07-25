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
			'password'         => '123',
			'charset'          => 'utf8',
		),

        'mail' => array(
            'class'         => 'ext.yii-mail.YiiMail',
            'transportType' => 'smtp',
            'viewPath'      => 'application.views.mail',
            'logging'       => false,
            'dryRun'        => false,
            'transportOptions' => GTTA_PRODUCTION ? array(
                    'host'         => 'mail.netprotect.ch',
                    'port'         => 25,
                    'username'     => 'web365p2',
                    'password'     => '6ghJZdGn',
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
            'lockFile'    => GTTA_PRODUCTION ? dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../tmp/gtta.email' : '/tmp/gtta.email',
            'systemEmail' => GTTA_PRODUCTION ? 'gtta@netprotect.ch' : 'gtta.test@yandex.ru',
            'maxAttempts' => 3,
        ),

        // checks automation
        'automation' => array(
            'minNotificationInterval' => 1, // 5 minutes

            'lockFile'    => GTTA_PRODUCTION ? dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../tmp/gtta.automation' : '/tmp/gtta.automation',
            'tempPath'    => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files/automation',
            'scriptsPath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../scripts',

            'interpreters' => array(
                'py' => array(
                    'path'     => GTTA_PRODUCTION ? 'C:\Python27\python.exe' : '/usr/bin/python',
                    'basePath' => GTTA_PRODUCTION ? 'C:\Python27' : '/usr/bin'
                ),
                'pl' => array(
                    'path'     => GTTA_PRODUCTION ? 'C:\Perl64\bin\perl.exe' : '/usr/bin/perl',
                    'basePath' => GTTA_PRODUCTION ? 'C:\Perl64' : '/usr/bin'
                )
            )
        ),

        // file cleaner
        'cleaner' => array(
            'lockFile' => GTTA_PRODUCTION ? dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../tmp/gtta.cleaner' : '/tmp/gtta.cleaner',
        ),

        'yiicPath' => dirname(__FILE__).'/../',

        'timeZone' => $mainConfig['params']['timeZone'],
    ),
);