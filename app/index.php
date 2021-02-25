<?php

require_once 'system/Loader.php';
$loader = new \app\system\Loader();
$config = $loader->getConfig();

require_once 'system/Bootstrap.php';
$bootstrap = new \app\system\Bootstrap($config);
$bootstrap->exec();

