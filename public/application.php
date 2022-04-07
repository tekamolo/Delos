<?php
define('DELOS_PROJECT_ROOT_PATH', dirname(dirname(__FILE__)));

require_once DELOS_PROJECT_ROOT_PATH . "/vendor/autoload.php";

$kernel = new \Delos\Kernel();
$kernel->setProjectRootPath(
    \Delos\Shared\Directory::createFromString(DELOS_PROJECT_ROOT_PATH)
);
$kernel->setRoutingFile(
    \Delos\Shared\File::createFromString(DELOS_PROJECT_ROOT_PATH . '/framework/routing.xml')
);
$kernel->setEnvironmentFile(\Delos\Shared\File::createFromString(DELOS_PROJECT_ROOT_PATH . "/.env"));
$kernel->boot();