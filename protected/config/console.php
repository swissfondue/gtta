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
		"db" => array(
			"connectionString" => $mainConfig["components"]["db"]["connectionString"],
			"username" => $mainConfig["components"]["db"]["username"],
			"password" => $mainConfig["components"]["db"]["password"],
			"charset" => $mainConfig["components"]["db"]["charset"],
		),

        "mail" => array(
            "class"         => "ext.yii-mail.YiiMail",
            "transportType" => "smtp",
            "viewPath"      => "application.views.mail",
            "logging"       => false,
            "dryRun"        => false,
            "transportOptions" => GTTA_PRODUCTION ? array(
                    "host"         => "mailbak.netprotect.ch",
                    "port"         => 25,
                    "username"     => "web365p2",
                    "password"     => "babuschka",
                    "encryption"   => "",
                ) :
                array(
                    "host"         => "smtp.yandex.ru",
                    "port"         => 465,
                    "username"     => "gtta.test@yandex.ru",
                    "password"     => "123321",
                    "encryption"   => "ssl",
                ),
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
        // email sender
        "email" => array(
            "lockFile"    => "/tmp/gtta.email",
            "systemEmail" => GTTA_PRODUCTION ? "gtta@netprotect.ch" : "gtta.test@yandex.ru",
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
            "keyFile" => BASE_DIR . "/security/keys/update-server.key",
            "directory" => "/tmp/gtta-update",
            "versions" => BASE_DIR . "/versions",
            "currentVersionLink" => BASE_DIR . "/current",
        ),

        // checks automation
        "automation" => array(
            "minNotificationInterval" => 5 * 60, // 5 minutes

            "lockFile" => "/tmp/gtta.automation",
            "tempPath" => BASE_DIR . "/files/automation",
            "scriptsPath" => VERSION_DIR . "/scripts",

            "interpreters" => array(
                "py" => array(
                    "path"     => "/usr/bin/python",
                    "basePath" => "/usr/bin"
                ),
                "pl" => array(
                    "path"     => "/usr/bin/perl",
                    "basePath" => "/usr/bin"
                ),
                "rb" => array(
                    "path"     => "/usr/bin/ruby",
                    "basePath" => "/usr/bin"
                )
            )
        ),

        "yiicPath" => dirname(__FILE__) . "/../",
        "attachments" => $mainConfig["params"]["attachments"],
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