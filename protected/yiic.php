<?php

defined('GTTA_PRODUCTION') or define('GTTA_PRODUCTION', false);
defined('GTTA_VIRTUAL') or define('GTTA_VIRTUAL', false);
defined('YII_DEBUG') or define('YII_DEBUG', !GTTA_PRODUCTION);

// change the following paths if necessary
$yiic=dirname(__FILE__).'/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

require_once($yiic);
