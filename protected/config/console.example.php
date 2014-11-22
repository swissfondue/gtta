<?php

$mainConfig = require(dirname(__FILE__) . "/main.php");

// This is the configuration for yiic console application.
return array(
	"basePath" => dirname(__FILE__) . "/..",
	"name" => $mainConfig["name"],
    "runtimePath" => $mainConfig["runtimePath"],

    // preloading components
	"preload" => array("log"),

    // autoloading model and component classes
	"import" => array(
        "application.forms.*",
		"application.models.*",
		"application.components.*",
        "application.components.formats.*",
        "application.components.processors.*",
        "application.components.reports.*",
        "application.components.reports.docx.*",
        "ext.yii-mail.YiiMailMessage",
	),

	// application components
	"components" => array(
		"db" => $mainConfig["components"]["db"],

        "mail" => array(
            "class" => "ext.yii-mail.YiiMail",
            "transportType" => "smtp",
            "viewPath" => "application.views.mail",
            "logging" => false,
            "dryRun" => false,
            "transportOptions" => array(
                "host" => "smtp.yandex.ru",
                "port" => "465",
                "username" => "gtta.test@yandex.ru",
                "password" => "123321",
                "encryption" => "ssl",
            )
        ),

        "urlManager" => $mainConfig["components"]["urlManager"],

        "log" => array(
			"class"  => "CLogRouter",
			"routes" => array(
				array(
					"class"   => "CFileLogRoute",
					"levels"  => "error, warning",
                    "logFile" => "console.log",
				),
			),
		),
	),

    // parameters
    "params" => array(
        "tmpPath" => $mainConfig["params"]["tmpPath"],

        // email sender
        "email" => array(
            "lockFile" => "/tmp/gtta.email",
            "systemEmail" => "gtta.test@yandex.ru",
            "maxAttempts" => 3,
        ),

        // vulnerability tracker
        "vulntracker" => array(
            "lockFile" => "/tmp/gtta.vulntracker",
        ),

        // project hold
        "projectHold" => array(
            "lockFile" => "/tmp/gtta.project-hold",
        ),

        "checkupdate" => array(
            "lockFile" => "/tmp/gtta.check-update",
        ),

        "stats" => array(
            "lockFile" => "/tmp/gtta.stats"
        ),

        "targetCheckSync" => array(
            "lockFile" => "/tmp/gtta.target-check-sync"
        ),

        "update" => array(
            "lockFile" => "/tmp/gtta.update",
            "keyFile" => BASE_DIR . "/security/keys/update-server.pub",
            "directory" => "/tmp/gtta-update",
            "versions" => BASE_DIR . "/versions",
            "currentVersionLink" => BASE_DIR . "/current",
            "deployConfig" => BASE_DIR . "/config/gtta.ini",
        ),

        "regenerate" => array(
            "lockFile" => "/tmp/gtta.regenerate",
        ),

        // checks automation
        "automation" => array(
            "minNotificationInterval" => 5 * 60, // 5 minutes
            "lockFile" => "/tmp/gtta.automation",
            "gtLockFile" => "/tmp/gtta.gt-automation",
            "filesPath" => BASE_DIR . "/files",
            "pidsPath" => "/tmp",
        ),

        // cleans unused files
        "filecleaner" => array(
            'lockFile' => '/tmp/gtta.file-cleaner'
        ),

        "yiicPath" => dirname(__FILE__) . "/../",
        "attachments" => $mainConfig["params"]["attachments"],
        "backups" => $mainConfig["params"]["backups"],
        "reports" => $mainConfig["params"]["reports"],
        "clientLogos" => $mainConfig["params"]["clientLogos"],
        "systemLogo" => $mainConfig["params"]["systemLogo"],

        "api" => array(
            "url" => "http://gta-update.does-it.net:8080",
        ),

        "community" => array(
            "url" => "http://community.gtta.net",
            "install" => array(
                "lockFile" => "/tmp/gtta.community-install",
            ),
            "share" => array(
                "lockFile" => "/tmp/gtta.community-share",
            ),
        ),

        "packages" => $mainConfig["params"]["packages"],
        "systemStatusLock" => $mainConfig["params"]["systemStatusLock"],
    ),
    
    "commandMap" => array(
        "migrate" => array(
            "class" => "system.cli.commands.MigrateCommand",
            "migrationPath" => "application.migrations",
            "migrationTable" => "migrations",
            "connectionID" => "db",
            "templateFile" => "application.migrations.template",
        ),
        "checkupdate" => array(
            "class" => "application.commands.CheckUpdateCommand",
        ),
    ),
);

?>