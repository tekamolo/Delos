<?php
declare(strict_types=1);

namespace Delos\Database;

use Delos\Shared\File;
use Illuminate\Database\Capsule\Manager;
use josegonzalez\Dotenv\Loader;

final class Connection
{
    function __construct(File $environmentFile)
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

    private function getDatabaseConfiguration(File $environmentFile): ?array
    {
        $Loader = new Loader($environmentFile->getPath());
        $Loader->parse();
        return $Loader->toArray();
    }
}