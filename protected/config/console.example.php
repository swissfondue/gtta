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
                "host" => "$DEPLOY_SMTP_SERVER",
                "port" => "$DEPLOY_SMTP_PORT",
                "username" => "$DEPLOY_SMTP_LOGIN",
                "password" => "$DEPLOY_SMTP_PASSWORD",
                "encryption" => "$DEPLOY_SMTP_ENCRYPTION",
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
            "systemEmail" => "$DEPLOY_SMTP_EMAIL",
            "maxAttempts" => 3,
        ),

        // vulnerability tracker
        "vulntracker" => array(
            "lockFile" => "/tmp/gtta.vulntracker",
        ),

        "checkupdate" => array(
            "lockFile" => "/tmp/gtta.check-update",
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
            "tempPath" => BASE_DIR . "/files/automation",
            "scriptsPath" => VERSION_DIR . "/scripts",

            "interpreters" => array(
                "py" => array(
                    "path"     => "/usr/bin/python",
                    "basePath" => "/usr/bin",
                    "params" => array(),
                    "env" => array(
                        "PYTHONPATH" => "/opt/gtta/scripts/lib:/opt/gtta/scripts/system/lib",
                    ),
                ),
                "pl" => array(
                    "path" => "/usr/bin/perl",
                    "basePath" => "/usr/bin",
                    "params" => array(
                        "-I/opt/gtta/scripts/lib",
                        "-I/opt/gtta/scripts/system/lib",
                    ),
                    "env" => array(),
                ),
            )
        ),

        "yiicPath" => dirname(__FILE__) . "/../",
        "attachments" => $mainConfig["params"]["attachments"],

        "api" => array(
            "url" => "$DEPLOY_API_URL",
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