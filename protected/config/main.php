<?php

define("YII_DEBUG", false);
define("YII_TRACE_LEVEL", 3);
defined("BASE_DIR") || define("BASE_DIR", "/opt/gtta");

$params = loadConfig(dirname(__FILE__), "params");

// main Web application configuration
return array(
	"basePath" => dirname(__FILE__) . "/..",
	"name"  => "GTTA",
    "charset" => "utf-8",
    "runtimePath" => BASE_DIR . "/runtime",

	// preloading components
	"preload" => array("log"),

	// autoloading model and component classes
	"import" => array(
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
	),

	// application components
	"components" => array(
        "session" => array(
            "class" => "CDbHttpSession",
            "sessionTableName" => "sessions",
            "connectionID" => "db",
            "autoCreateSessionTable" => true,
            "timeout" => 60 * 60, // 1 hour
        ),

        "request" => array(
            "enableCsrfValidation" => true,
        ),

		"user" => array(
            "class" => "WebUser",
            "autoUpdateFlash" => true,
		),

		"urlManager" => array(
            "baseUrl" => $params["baseUrl"],
			"urlFormat" => "path",
            "showScriptName" => false,
            "rules" => loadConfig(dirname(__FILE__), "routes"),
		),

        "db" => loadConfig(dirname(__FILE__), "db"),

		"errorHandler" => array(
            "errorAction" => "app/error",
        ),

		"log" => array(
			"class"  => "CLogRouter",
			"routes" => array(
				array(
					"class" => "CFileLogRoute",
					"levels" => "error, warning",
				),
			),
		),
	),

    // parameters
	"params" => $params,

    // default controller name
    "defaultController" => "app",

    // language settings
    "sourceLanguage" => "en",
    "language"       => "en",
);

?>
