<?php

define("YII_DEBUG", false);
define("YII_TRACE_LEVEL", 3);
defined("BASE_DIR") || define("BASE_DIR", "/opt/gtta");

$params = loadConfig(dirname(__FILE__), "params");

// main Web application configuration
return [
	"basePath" => dirname(__FILE__) . "/..",
	"name"  => "GTTA",
    "charset" => "utf-8",
    "runtimePath" => BASE_DIR . "/runtime",

	// preloading components
	"preload" => ["log"],

	// autoloading model and component classes
	"import" => [
        "application.forms.*",
		"application.models.*",
		"application.components.*",
        "application.components.formats.*",
        "application.components.filters.*",
        "application.components.reports.*",
        "application.components.reports.docx.*",
        "application.extensions.PHPRtfLite.*",
        "application.extensions.PHPRtfLite.PHPRtfLite.*",
        "application.jobs.*",
	],

	// application components
	"components" => [
        "session" => [
            "class" => "CDbHttpSession",
            "sessionTableName" => "sessions",
            "connectionID" => "db",
            "autoCreateSessionTable" => true,
            "timeout" => 0,
        ],

        "request" => [
            "enableCsrfValidation" => true,
        ],

		"user" => [
            "class" => "WebUser",
            "autoUpdateFlash" => true,
		],

		"urlManager" => [
            "baseUrl" => $params["baseUrl"],
			"urlFormat" => "path",
            "showScriptName" => false,
            "rules" => loadConfig(dirname(__FILE__), "routes"),
		],

        "db" => loadConfig(dirname(__FILE__), "db"),

		"errorHandler" => [
            "errorAction" => "app/error",
        ],

		"log" => [
			"class"  => "CLogRouter",
			"routes" => [
				[
					"class" => "CFileLogRoute",
					"levels" => "error, warning",
				],
			],
		],
	],

    // parameters
	"params" => $params,

    // default controller name
    "defaultController" => "app",

    // language settings
    "sourceLanguage" => "en",
    "language"       => "en",
];