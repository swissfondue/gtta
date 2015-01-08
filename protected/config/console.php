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
        "application.jobs.*",
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
            "transportOptions" => array()
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
    "params" => $mainConfig["params"],
    
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