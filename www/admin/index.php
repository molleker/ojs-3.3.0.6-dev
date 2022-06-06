<?php
/*
ini_set('display_errors','On');
error_reporting(7);*/

// comment out the following two lines when deployed to production
define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../yii/vendor/autoload.php');
require(__DIR__ . '/../../yii/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../../yii/config/web.php');
(new yii\web\Application($config))->run();

