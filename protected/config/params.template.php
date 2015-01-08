<?php

defined("BASE_DIR") or define("BASE_DIR", "/opt/gtta");

return array(
    "baseUrl" => "DEPLOY_BASE_URL",
    "entriesPerPage" => 10,
    "maxCheckboxes" => 3,

    "fonts" => array(
        "path" => dirname(__FILE__) . "/../../fonts",
    ),

    "attachments" => array(
        "path" => BASE_DIR . "/files/attachments",
        "maxSize" => 100 * 1024 * 1024, // 100 megabytes
    ),

    "backups" => array(
        "maxSize" => 100 * 1024 * 1024, // 100 megabytes
        'tmpFilesPath' => '/tmp/backups',
    ),

    "clientLogos" => array(
        "path" => BASE_DIR . "/files/logos",
        "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        "tmpFilesPath" => '/tmp/logos'
    ),

    "systemLogo" => array(
        "defaultPath" => BASE_DIR . "/current/web/images/logo.png",
        "path" => BASE_DIR . "/files/logos/system",
        "maxSize" => 10 * 1024 * 1024, // 10 megabytes
    ),

    "reports" => array(
        "fontSize" => 12,
        "minFontSize" => 1,
        "maxFontSize" => 50,

        "font" => "Helvetica",
        "fonts" => array(
            "Arial",
            "Bookman Old Style",
            "Courier",
            "Courier New",
            "Garamond",
            "Helvetica",
            "Microsoft Sans Serif",
            "Palatino",
            "Times",
            "Verdana",
        ),

        "pageMargin" => 1.5,
        "minPageMargin" => 0.0,
        "maxPageMargin" => 5.0,

        "cellPadding" => 0.2,
        "minCellPadding" => 0.0,
        "maxCellPadding" => 5.0,

        "headerImages" => array(
            "path" => BASE_DIR . "/files/header-images",
            "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        ),

        "ratingImages" => array(
            "path" => BASE_DIR . "/files/rating-images",
            "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        ),

        "file" => array(
            "path" => BASE_DIR . "/files/report-templates",
            "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        ),

        "tmpFilesPath" => "/tmp/report-files"
    ),

    "tmpPath" => "/tmp",

    "security" => array(
        "ca" => BASE_DIR . "/security/ca/ca.crt",
        "caKey" => BASE_DIR . "/security/ca/ca.key",
    ),

    "packages" => array(
        "path" => array(
            "scripts" => BASE_DIR . "/scripts",
            "libraries" => BASE_DIR . "/scripts/lib",
        ),
        "tmpPath" => "/tmp/gtta-package",
        "maxSize" => 100 * 1024 * 1024, // 100 megabytes
    ),

    "filesPath" => BASE_DIR . "/current/web/files",
    "systemStatusLock" => "/tmp/gtta-status.lock",

    "update" => array(
        "keyFile" => BASE_DIR . "/security/keys/update-server.pub",
        "directory" => "/tmp/gtta-update",
        "versions" => BASE_DIR . "/versions",
        "currentVersionLink" => BASE_DIR . "/current",
        "deployConfig" => BASE_DIR . "/config/gtta.ini",
    ),

    "automation" => array(
        "minNotificationInterval" => 5 * 60, // 5 minutes
        "filesPath" => BASE_DIR . "/files",
        "pidsPath" => "/tmp",
    ),

    "yiicPath" => dirname(__FILE__) . "/../",

    "api" => array(
        "url" => "http://gta-update.does-it.net:8080",
    ),

    "community" => array(
        "url" => "http://community.gtta.net",
    ),
);
