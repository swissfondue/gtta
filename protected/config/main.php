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
        'application.components.processors.*',
        'application.extensions.PHPRtfLite.*',
        'application.extensions.PHPRtfLite.PHPRtfLite.*',
	),

	// application components
	'components' => array(
        'session' => array(
            'class'                  => 'CDbHttpSession',
            'sessionTableName'       => 'sessions',
            'connectionID'           => 'db',
            'autoCreateSessionTable' => true,
            'timeout'                => 60 * 60, // 1 hour
        ),

        'request' => array(
            'enableCsrfValidation' => true,
        ),

		'user' => array(
            'class'           => 'WebUser',
            'autoUpdateFlash' => true,
		),

		'urlManager' => array(
            'baseUrl'        => GTTA_PRODUCTION ? ( GTTA_VIRTUAL ? 'https://gtta.box' : 'https://gtta.kicks-ass.net' ) : 'https://gtta.local',
			'urlFormat'      => 'path',
            'showScriptName' => false,
            'rules'          => array(
                // account
                '<action:(login|logout)>' => 'app/<action>',
                'account' => 'account/edit',
                'account/certificate' => 'account/certificate',
                'account/restore' => 'account/restore',
                'account/restore/<code:[a-z\d]+>' => 'account/changepassword',

                // misc
                'app/l10n.js'     => 'app/l10n',
                'app/object-list' => 'app/objectlist',

                // projects
                'projects/<page:\d+>'         => 'project/index',
                'projects'                    => 'project/index',
                'project/<id:\d+>/<page:\d+>' => 'project/view',
                'project/<id:\d+>'            => 'project/view',
                'project/<id:\d+>/edit'       => 'project/edit',
                'project/new'                 => 'project/edit',
                'project/control'             => 'project/control',

                // guided test
                'project/<id:\d+>/gt' => 'project/gt',
                'project/<id:\d+>/gt/module/<module:\d+>/check/<check:\d+>/control' => 'project/gtcontrolcheck',
                'project/<id:\d+>/gt/module/<module:\d+>/check/<check:\d+>/save' => 'project/gtsavecheck',
                'project/<id:\d+>/gt/module/<module:\d+>/check/<check:\d+>/update' => 'project/gtupdatechecks',
                'project/<id:\d+>/gt/module/<module:\d+>/check/<check:\d+>/attachment/new' => 'project/gtuploadattachment',
                'project/gt-attachment/<path:[a-z\d]+>/download' => 'project/gtattachment',
                'project/gt-attachment/control' => 'project/gtcontrolattachment',
                'project/gt-target/control' => 'project/gtcontroltarget',

                // project details
                'project/<id:\d+>/details/<page:\d+>'       => 'project/details',
                'project/<id:\d+>/details'                  => 'project/details',
                'project/<id:\d+>/detail/<detail:\d+>/edit' => 'project/editdetail',
                'project/<id:\d+>/detail/new'               => 'project/editdetail',
                'project/detail/control'                    => 'project/controldetail',

                // project users
                'project/<id:\d+>/users/<page:\d+>' => 'project/users',
                'project/<id:\d+>/users'            => 'project/users',
                'project/<id:\d+>/user/add'         => 'project/adduser',
                'project/<id:\d+>/user/control'     => 'project/controluser',

                // project target and checks
                'project/<id:\d+>/target/<target:\d+>/<page:\d+>'                                            => 'project/target',
                'project/<id:\d+>/target/<target:\d+>'                                                       => 'project/target',
                'project/<id:\d+>/target/<target:\d+>/edit'                                                  => 'project/edittarget',
                'project/<id:\d+>/target/new'                                                                => 'project/edittarget',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>'                                  => 'project/checks',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/save'                             => 'project/savecategory',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/check/<check:\d+>/save'           => 'project/savecheck',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/check/<check:\d+>/control'        => 'project/controlcheck',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/update'                           => 'project/updatechecks',
                'project/<id:\d+>/target/<target:\d+>/check/<category:\d+>/check/<check:\d+>/attachment/new' => 'project/uploadattachment',
                'project/attachment/<path:[a-z\d]+>/download'                                                => 'project/attachment',
                'project/attachment/control'                                                                 => 'project/controlattachment',
                'project/target/control'                                                                     => 'project/controltarget',
                'project/search'                                                                             => 'project/search',

                // reports
                'reports/project'     => 'report/project',
                'reports/comparison'  => 'report/comparison',
                'reports/fulfillment' => 'report/fulfillment',
                'reports/risk-matrix' => 'report/riskmatrix',
                'reports/effort'      => 'report/effort',
                'reports/vuln-export' => 'report/vulnexport',

                // vulnerability tracker
                'vuln-tracker'                                             => 'vulntracker/index',
                'vuln-tracker/<id:\d+>/<page:\d+>'                         => 'vulntracker/vulns',
                'vuln-tracker/<id:\d+>'                                    => 'vulntracker/vulns',
                'vuln-tracker/<id:\d+>/vuln/<target:\d+>/<check:\d+>/edit' => 'vulntracker/edit',

                // clients
                'clients/<page:\d+>'       => 'client/index',
                'clients'                  => 'client/index',
                'client/<id:\d+>'          => 'client/view',
                'client/<id:\d+>/edit'     => 'client/edit',
                'client/new'               => 'client/edit',
                'client/control'           => 'client/control',
                'client/search'            => 'client/search',
                'client/<id:\d+>/logo/new' => 'client/uploadlogo',
                'client/<id:\d+>/logo'     => 'client/logo',
                'client/logo/control'      => 'client/controllogo',

                // settings
                'settings' => 'settings/edit',

                // checks
                'checks/<page:\d+>'         => 'check/index',
                'checks'                    => 'check/index',
                'check/<id:\d+>/<page:\d+>' => 'check/view',
                'check/<id:\d+>'            => 'check/view',
                'check/<id:\d+>/edit'       => 'check/edit',
                'check/new'                 => 'check/edit',
                'check/control'             => 'check/control',
                'check/search'              => 'check/search',

                // check controls
                'check/<id:\d+>/control/<control:\d+>/<page:\d+>' => 'check/viewcontrol',
                'check/<id:\d+>/control/<control:\d+>'            => 'check/viewcontrol',
                'check/<id:\d+>/control/<control:\d+>/edit'       => 'check/editcontrol',
                'check/<id:\d+>/control/new'                      => 'check/editcontrol',
                'check/control/control'                           => 'check/controlcontrol',

                // checks
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/edit' => 'check/editcheck',
                'check/<id:\d+>/control/<control:\d+>/check/new'              => 'check/editcheck',
                'check/<id:\d+>/control/<control:\d+>/check/copy'             => 'check/copycheck',
                'check/control/check/control'                                 => 'check/controlcheck',

                // check results
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/results/<page:\d+>'       => 'check/results',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/results'                  => 'check/results',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/result/<result:\d+>/edit' => 'check/editresult',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/result/new'               => 'check/editresult',
                'check/control/check/result/control'                                              => 'check/controlresult',
                
                // check solutions
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solutions/<page:\d+>'         => 'check/solutions',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solutions'                    => 'check/solutions',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solution/<solution:\d+>/edit' => 'check/editsolution',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solution/new'                 => 'check/editsolution',
                'check/control/check/solution/control'                                                => 'check/controlsolution',

                // check scripts
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/scripts/<page:\d+>'       => 'check/scripts',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/scripts'                  => 'check/scripts',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/edit' => 'check/editscript',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/new'               => 'check/editscript',
                'check/control/check/script/control'                                              => 'check/controlscript',

                // check inputs
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/inputs/<page:\d+>'      => 'check/inputs',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/inputs'                 => 'check/inputs',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/input/<input:\d+>/edit' => 'check/editinput',
                'check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/input/new'              => 'check/editinput',
                'check/control/check/input/control' => 'check/controlinput',

                // references
                'references/<page:\d+>'   => 'reference/index',
                'references'              => 'reference/index',
                'reference/<id:\d+>/edit' => 'reference/edit',
                'reference/new'           => 'reference/edit',
                'reference/control'       => 'reference/control',

                // guided test templates
                'gt-templates/<page:\d+>' => 'gt/index',
                'gt-templates' => 'gt/index',
                'gt-template/<id:\d+>/<page:\d+>' => 'gt/view',
                'gt-template/<id:\d+>' => 'gt/view',
                'gt-template/<id:\d+>/edit' => 'gt/edit',
                'gt-template/new' => 'gt/edit',
                'gt-template/control' => 'gt/control',

                // guided test types
                'gt-template/<id:\d+>/type/<type:\d+>/<page:\d+>' => 'gt/viewtype',
                'gt-template/<id:\d+>/type/<type:\d+>' => 'gt/viewtype',
                'gt-template/<id:\d+>/type/<type:\d+>/edit' => 'gt/edittype',
                'gt-template/<id:\d+>/type/new' => 'gt/edittype',
                'gt-template/type/control' => 'gt/controltype',

                // guided test modules
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/<page:\d+>' => 'gt/viewmodule',
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>' => 'gt/viewmodule',
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/edit' => 'gt/editmodule',
                'gt-template/<id:\d+>/type/<type:\d+>/module/new' => 'gt/editmodule',
                'gt-template/type/module/control' => 'gt/controlmodule',

                // guided test module checks
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/check/<check:\d+>/edit' => 'gt/editcheck',
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/check/new' => 'gt/editcheck',
                'gt-template/type/module/check/control' => 'gt/controlcheck',

                // guided test check dependencies
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/check/<check:\d+>/dependencies/<page:\d+>' => 'gt/dependencies',
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/check/<check:\d+>/dependencies' => 'gt/dependencies',
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/check/<check:\d+>/dependency/<dependency:\d+>/edit' => 'gt/editdependency',
                'gt-template/<id:\d+>/type/<type:\d+>/module/<module:\d+>/check/<check:\d+>/dependency/new' => 'gt/editdependency',

                // report templates
                'report-templates/<page:\d+>'   => 'reporttemplate/index',
                'report-templates'              => 'reporttemplate/index',
                'report-template/<id:\d+>/edit' => 'reporttemplate/edit',
                'report-template/new'           => 'reporttemplate/edit',
                'report-template/control'       => 'reporttemplate/control',
                'report-template/<id:\d+>/header/new' => 'reporttemplate/uploadheaderimage',
                'report-template/<id:\d+>/header'     => 'reporttemplate/headerimage',
                'report-template/header/control'      => 'reporttemplate/controlheaderimage',

                // summary blocks
                'report-template/<id:\d+>/summary-blocks/<page:\d+>'        => 'reporttemplate/summary',
                'report-template/<id:\d+>/summary-blocks'                   => 'reporttemplate/summary',
                'report-template/<id:\d+>/summary-block/<summary:\d+>/edit' => 'reporttemplate/editsummary',
                'report-template/<id:\d+>/summary-block/new'                => 'reporttemplate/editsummary',
                'report-template/summary-block/control'                     => 'reporttemplate/controlsummary',

                // report template sections
                'report-template/<id:\d+>/sections/<page:\d+>'        => 'reporttemplate/sections',
                'report-template/<id:\d+>/sections'                   => 'reporttemplate/sections',
                'report-template/<id:\d+>/section/<section:\d+>/edit' => 'reporttemplate/editsection',
                'report-template/<id:\d+>/section/new'                => 'reporttemplate/editsection',
                'report-template/section/control'                     => 'reporttemplate/controlsection',

                // risk classification categories (templates)
                'risks/<page:\d+>'         => 'risk/index',
                'risks'                    => 'risk/index',
                'risk/<id:\d+>/<page:\d+>' => 'risk/view',
                'risk/<id:\d+>'            => 'risk/view',
                'risk/<id:\d+>/edit'       => 'risk/edit',
                'risk/new'                 => 'risk/edit',
                'risk/control'             => 'risk/control',

                // risk categories
                'risk/<id:\d+>/category/<category:\d+>/edit' => 'risk/editcategory',
                'risk/<id:\d+>/category/new'                 => 'risk/editcategory',
                'risk/<id:\d+>/category/control'             => 'risk/controlcategory',

                // users
                'users/<page:\d+>'   => 'user/index',
                'users'              => 'user/index',
                'user/<id:\d+>/edit' => 'user/edit',
                'user/new'           => 'user/edit',
                'user/control'       => 'user/control',
                'user/<id:\d+>/certificate' => 'user/certificate',

                // user projects
                'user/<id:\d+>/projects/<page:\d+>' => 'user/projects',
                'user/<id:\d+>/projects'            => 'user/projects',
                'user/<id:\d+>/project/add'         => 'user/addproject',
                'user/<id:\d+>/project/control'     => 'user/controlproject',
                'user/<id:\d+>/object-list'         => 'user/objectlist',

                // backup
                'backup'  => 'backup/backup',
                'restore' => 'backup/restore',

                // system monitor
                'processes'       => 'monitor/processes',
                'process/control' => 'monitor/controlprocess',
                'sessions'        => 'monitor/sessions',

                // history
                'history/logins/<page:\d+>' => 'history/logins',
                'history/logins'            => 'history/logins',
            ),
		),

        'db' => array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=gtta',
			'username'         => 'gtta',
			'password'         => GTTA_VIRTUAL ? '3yNeMw4sMaj6TC8gJ2Ecvh2GF' : '123',
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
        'maxCheckboxes' => 3,

        'fonts' => array(
            'path' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../fonts',
        ),

        'attachments' => array(
            'path'    => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files/attachments',
            'maxSize' => 100 * 1024 * 1024, // 100 megabytes
        ),

        'backups' => array(
            'maxSize' => 100 * 1024 * 1024, // 100 megabytes
        ),

        'clientLogos' => array(
            'path' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files/logos',
            'maxSize' => 10 * 1024 * 1024, // 10 megabytes
        ),

        'reports' => array(
            'fontSize' => 12,
            'minFontSize' => 1,
            'maxFontSize' => 50,

            'font' => 'Helvetica',
            'fonts' => array(
                'Arial',
                'Bookman Old Style',
                'Courier',
                'Courier New',
                'Garamond',
                'Helvetica',
                'Microsoft Sans Serif',
                'Palatino',
                'Times',
                'Verdana',
            ),

            'pageMargin' => 1.5,
            'minPageMargin' => 0.0,
            'maxPageMargin' => 5.0,

            'cellPadding' => 0.2,
            'minCellPadding' => 0.0,
            'maxCellPadding' => 5.0,

            'headerImages' => array(
                'path'    => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files/header-images',
                'maxSize' => 10 * 1024 * 1024, // 10 megabytes
            ),
        ),

        'tmpPath'            => '/tmp',
        'collapseCheckCount' => 20,

        'automation' => array(
            'scriptsPath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../scripts',
        ),
    ),

    // default controller name
    'defaultController' => 'app',

    // maintenance mode
    'catchAllRequest' => file_exists(dirname(__FILE__) . '/.maintenance') ? array( 'app/maintenance' ) : null,

    // language settings
    'sourceLanguage' => 'en',
    'language'       => 'en',
);
