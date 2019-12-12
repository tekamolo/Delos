<?php

namespace Delos\Database;

use Illuminate\Database\Capsule\Manager;

class Connection
{
    function __construct()
    {
        $env = $this->getDatabaseConfiguration();
        $capsule = new Manager();
        $capsule->addConnection([
            'driver' => $env['DB_CONNECTION'],
            'host' => $env['DB_HOST'],
            'port' => $env['DB_PORT'],
            'database' => $env['DB_DATABASE'],
            'username' => $env['DB_USERNAME'],
            'password' => $env['DB_PASSWORD'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    private function getDatabaseConfiguration()
    {
        $Loader = new \josegonzalez\Dotenv\Loader(PROJECT_ROOT_PATH.'/.env');
        $Loader->parse();
        return $Loader->toArray();
    }
}