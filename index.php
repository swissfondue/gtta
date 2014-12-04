<?php

$common = dirname(__FILE__) . "/protected/common.php";
$config = dirname(__FILE__) . "/protected/config/main.php";
$yii = dirname(__FILE__) . "/protected/framework/yii.php";

require_once($common);
require_once($config);
require_once($yii);

Yii::createWebApplication($config)->run();
