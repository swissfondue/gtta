<?php

// main Web application configuration
return array(
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'     => 'GTTA',
    'charset'  => 'utf-8',

	// preloading components
	'preload' => array( 'log' ),

	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
	),

	// application components
	'components' => array(
        'request' => array(
            'enableCsrfValidation' => true,
        ),

		'user' => array(
            'class'           => 'WebUser',
            'autoUpdateFlash' => true,
		),

		'urlManager' => array(
			'urlFormat'      => 'path',
            'showScriptName' => false,
            'rules'          => array(
                // account
                '<action:(login|logout)>' => 'app/<action>',
                'app/l10n.js'             => 'app/l10n',

                // projects
                'projects/<page:\d+>'                                                              => 'project/index',
                'projects'                                                                         => 'project/index',
                'project/<id:\d+>/<page:\d+>'                                                      => 'project/view',
                'project/<id:\d+>'                                                                 => 'project/view',
                'project/<id:\d+>/edit'                                                            => 'project/edit',
                'project/new'                                                                      => 'project/edit',
                'project/<id:\d+>/details/<page:\d+>'                                              => 'project/details',
                'project/<id:\d+>/details'                                                         => 'project/details',
                'project/<id:\d+>/detail/<detail:\d+>/edit'                                        => 'project/editdetail',
                'project/<id:\d+>/detail/new'                                                      => 'project/editdetail',
                'project/<id:\d+>/target/<target:\d+>/<page:\d+>'                                  => 'project/target',
                'project/<id:\d+>/target/<target:\d+>'                                             => 'project/target',
                'project/<id:\d+>/target/<target:\d+>/edit'                                        => 'project/edittarget',
                'project/<id:\d+>/target/edit'                                                     => 'project/edittarget',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>'                        => 'project/checks',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/save'                   => 'project/savecategory',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/check/<check:\d+>/save' => 'project/savecheck',

                // clients
                'clients/<page:\d+>'   => 'client/index',
                'clients'              => 'client/index',
                'client/<id:\d+>'      => 'client/view',
                'client/<id:\d+>/edit' => 'client/edit',
                'client/new'           => 'client/edit',

                // checks
                'checks/<page:\d+>'                     => 'check/index',
                'checks'                                => 'check/index',
                'check/<id:\d+>/<page:\d+>'             => 'check/view',
                'check/<id:\d+>'                        => 'check/view',
                'check/<id:\d+>/edit'                   => 'check/edit',
                'check/new'                             => 'check/edit',
                'check/<id:\d+>/check/<check:\d+>/edit' => 'check/editcheck',
                'check/<id:\d+>/check/new'              => 'check/editcheck',

                // check results
                'check/<id:\d+>/check/<check:\d+>/results/<page:\d+>'       => 'check/results',
                'check/<id:\d+>/check/<check:\d+>/results'                  => 'check/results',
                'check/<id:\d+>/check/<check:\d+>/result/<result:\d+>/edit' => 'check/editresult',
                'check/<id:\d+>/check/<check:\d+>/result/new'               => 'check/editresult',
                
                // check solutions
                'check/<id:\d+>/check/<check:\d+>/solutions/<page:\d+>'         => 'check/solutions',
                'check/<id:\d+>/check/<check:\d+>/solutions'                    => 'check/solutions',
                'check/<id:\d+>/check/<check:\d+>/solution/<solution:\d+>/edit' => 'check/editsolution',
                'check/<id:\d+>/check/<check:\d+>/solution/new'                 => 'check/editsolution',
                
                // check inputs
                'check/<id:\d+>/check/<check:\d+>/inputs/<page:\d+>'      => 'check/inputs',
                'check/<id:\d+>/check/<check:\d+>/inputs'                 => 'check/inputs',
                'check/<id:\d+>/check/<check:\d+>/input/<input:\d+>/edit' => 'check/editinput',
                'check/<id:\d+>/check/<check:\d+>/input/new'              => 'check/editinput',

                // users
                'users/<page:\d+>'   => 'user/index',
                'users'              => 'user/index',
                'user/<id:\d+>/edit' => 'user/edit',
                'user/new'           => 'user/edit',
            ),
		),

        'db' => array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=gtta',
			'username'         => 'gtta',
			'password'         => '123',
			'charset'          => 'utf8',
		),

		'errorHandler' => array(
            'errorAction' => 'app/error',
        ),

		'log' => array(
			'class'  => 'CLogRouter',
			'routes' => array(
				array(
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
                /*array(
                    'class'   => 'CProfileLogRoute',
                    'enabled' => true,
                ),
                array(
                    'class' => 'CWebLogRoute',
                ),*/
			),
		),
	),

    // parameters
	'params' => array(
        'entriesPerPage' => 10,
    ),

    // default controller name
    'defaultController' => 'app',

    // maintenance mode
    'catchAllRequest' => file_exists(dirname(__FILE__) . '/.maintenance') ? array( 'app/maintenance' ) : null,

    // language settings
    'sourceLanguage' => 'en',
    'language'       => 'en',
);
