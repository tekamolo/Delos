<?php
declare(strict_types=1);

namespace Delos\Database;

use Illuminate\Database\Capsule\Manager;
use josegonzalez\Dotenv\Loader;

class Connection
{
    function __construct(string $environmentFile)
    {
        $env = $this->getDatabaseConfiguration($environmentFile);
        $capsule = new Manager();
        $capsule->addConnection([
            'driver' => $env['MYSQL_CONNECTION'],
            'host' => $env['MYSQL_HOST'],
            'port' => $env['MYSQL_PORT'],
            'database' => $env['MYSQL_DATABASE'],
            'username' => $env['MYSQL_USER'],
            'password' => $env['MYSQL_PASSWORD'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    private function getDatabaseConfiguration(string $environmentFile): ?array
    {
        $Loader = new Loader($environmentFile);
        $Loader->parse();
        return $Loader->toArray();
    }
}