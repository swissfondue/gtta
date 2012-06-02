<?php

// messages configuration
return array(
    'sourcePath'  => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'messagePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../messages',

    'languages' => array(
        'en',
        'de',
    ),

    'fileTypes' => array(
        'php',
    ),

    'exclude' => array(
        '/framework/',
    ),

    'overwrite' => true,
    'removeOld' => true,
);
