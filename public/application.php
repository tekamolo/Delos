<?php
define('DELOS_PROJECT_ROOT_PATH', dirname(dirname(__FILE__)));

require_once DELOS_PROJECT_ROOT_PATH."/vendor/autoload.php";

$kernel = new \Delos\Kernel();
$kernel->setProjectRootPath(DELOS_PROJECT_ROOT_PATH);
$kernel->setRoutingFile('/framework/routing.xml');
$kernel->setEnvironmentFile(DELOS_PROJECT_ROOT_PATH."/.env");
$kernel->boot();