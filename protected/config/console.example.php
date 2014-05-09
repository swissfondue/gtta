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

        "stats" => array(
            "lockFile" => "/tmp/gtta.stats"
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

        "yiicPath" => dirname(__FILE__) . "/../",
        "attachments" => $mainConfig["params"]["attachments"],

        "api" => array(
            "url" => "$DEPLOY_API_URL",
        ),

        "community" => array(
            "url" => "$DEPLOY_COMMUNITY_URL",
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