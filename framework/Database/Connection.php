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
            'driver' => $env['DATABASE'],
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

    private function getDatabaseConfiguration(string $environmentFile): ?array
    {
        $Loader = new Loader($environmentFile);
        $Loader->parse();
        return $Loader->toArray();
    }
}