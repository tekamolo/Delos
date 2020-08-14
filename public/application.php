<?php
define('PROJECT_ROOT_PATH', dirname(dirname(__FILE__)));

require_once PROJECT_ROOT_PATH."/framework/autoload.php";
require_once PROJECT_ROOT_PATH."/vendor/autoload.php";

$kernel = new \Delos\Kernel();
$kernel->setProjectFolder(PROJECT_ROOT_PATH);
$kernel->setRoutingFile('/framework/routing.xml');
$kernel->boot();