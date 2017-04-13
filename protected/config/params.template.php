<?php

defined("BASE_DIR") or define("BASE_DIR", "/opt/gtta");

return [
    "baseUrl" => "",

    "entriesPerPage" => 20,
    "limitedListEntriesCount" => 5,
    "maxCheckboxes" => 3,

    "fonts" => [
        "path" => dirname(__FILE__) . "/../../fonts",
    ],

    "attachments" => [
        "path" => BASE_DIR . "/files/attachments",
        "maxSize" => 100 * 1024 * 1024, // 100 megabytes
    ],

    "backups" => [
        "maxSize" => 1024 * 1024 * 1024, // 1G
        'path' => BASE_DIR . "/files/backups",
        'tmpFilesPath' => '/tmp/backups',
    ],

    "clientLogos" => [
        "path" => BASE_DIR . "/files/logos",
        "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        "tmpFilesPath" => '/tmp/logos'
    ],

    "systemLogo" => [
        "defaultPath" => BASE_DIR . "/current/web/images/logo.png",
        "path" => BASE_DIR . "/files/logos/system",
        "maxSize" => 10 * 1024 * 1024, // 10 megabytes
    ],

    "reports" => [
        "fontSize" => 12,
        "minFontSize" => 1,
        "maxFontSize" => 50,

        "font" => "Helvetica",
        "fonts" => [
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
        ],

        "pageMargin" => 1.5,
        "minPageMargin" => 0.0,
        "maxPageMargin" => 5.0,

        "cellPadding" => 0.2,
        "minCellPadding" => 0.0,
        "maxCellPadding" => 5.0,

        "headerImages" => [
            "path" => BASE_DIR . "/files/header-images",
            "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        ],

        "ratingImages" => [
            "path" => BASE_DIR . "/files/rating-images",
            "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        ],

        "file" => [
            "path" => BASE_DIR . "/files/report-templates",
            "maxSize" => 10 * 1024 * 1024, // 10 megabytes
        ],

        "tmpFilesPath" => "/tmp/report-files"
    ],

    "tmpPath" => "/tmp",

    "security" => [
        "ca" => BASE_DIR . "/security/ca/ca.crt",
        "caKey" => BASE_DIR . "/security/ca/ca.key",
    ],

    "packages" => [
        "path" => [
            "scripts" => BASE_DIR . "/scripts",
            "libraries" => BASE_DIR . "/scripts/lib",
        ],
        "tmpPath" => "/tmp/gtta-package",
        "maxSize" => 1024 * 1024 * 1024, // 1 Gb,
        "installerLock" => "/tmp/installer.lock",
        "git" => [
            "key" => "scripts_rsa",
            "scripts" => [
                "path" => BASE_DIR . "/current/tools/git",
                "init" => "init.sh",
                "configure" => "configure.sh",
                "sync" => "sync.sh",
            ],
        ],
    ],

    "system" => [
        "filesPath" => BASE_DIR . "/files/system",
    ],

    "filesPath" => BASE_DIR . "/current/web/files",
    "systemStatusLock" => "/tmp/gtta-status.lock",

    "update" => [
        "keyFile" => BASE_DIR . "/security/keys/update-server.pub",
        "directory" => "/tmp/gtta-update",
        "versions" => BASE_DIR . "/versions",
        "currentVersionLink" => BASE_DIR . "/current",
        "deployConfig" => BASE_DIR . "/config/gtta.ini",
    ],

    "automation" => [
        "minNotificationInterval" => 5 * 60, // 5 minutes
        "filesPath" => BASE_DIR . "/files",
        "pidsPath" => "/tmp",
    ],

    "yiicPath" => dirname(__FILE__) . "/../",

    "api" => [
        "url" => "http://update.phishing-server.com:80",
        "regKey" => "s8cowl2sv3l64menxb0jvqhci2yop7i0",
    ],

    "community" => [
        "url" => "http://community.gtta.net",
    ],

    "bgLogsPath" => BASE_DIR . "/runtime/bg",

    "os" => [
        "type" => BASE_DIR . "/config/type"
    ],

    "issue.field_length" => 20,
];
