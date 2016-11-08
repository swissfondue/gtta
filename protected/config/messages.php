<?php

// messages configuration
return [
    'sourcePath'  => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'messagePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../messages',

    'languages' => [
        'en',
        'de',
    ],

    'fileTypes' => [
        'php',
    ],

    'exclude' => [
        '/framework/',
    ],

    'overwrite' => true,
    'removeOld' => true,
];
