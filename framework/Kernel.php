<?php
declare(strict_types=1);

namespace Delos;

use Delos\Database\Connection;

final class Kernel
{
    private string $projectRootPath;
    private string $routingFile;
    private string $environmentFile;
    private array $nameSpacesBase;

    public function getRoutingFile(): string
    {
        return $this->routingFile;
    }

    public function setRoutingFile(string $routingFile): void
    {
        $this->routingFile = $routingFile;
    }

    public function getEnvironmentFile(): string
    {
        return $this->environmentFile;
    }

    public function setEnvironmentFile(string $environmentFile): void
    {
        $this->environmentFile = $environmentFile;
    }

    public function setProjectRootPath($projectRootPath)
    {
        $this->projectRootPath = $projectRootPath;
    }

    public function __construct()
    {
    }

    private function setConstants(): void
    {
    }

    private function loadAutoloaders(): void
    {
        new Connection($this->environmentFile); //booting eloquent
    }

    private function setConfigurations(): void
    {
        /**
         * Configurations should go here, whether they are developing, test or production
         */
        if (file_exists($this->projectRootPath . '/composer.json')) {
            $content = file_get_contents($this->projectRootPath . '/composer.json');
            $content = json_decode($content, true);
            if (!empty($content["autoload"]["psr-4"])) {
                foreach ($content["autoload"]["psr-4"] as $namespace => $src) {
                    $this->nameSpacesBase[] = $namespace . "\\";
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function boot(): void
    {
        $this->setConstants();
        $this->loadAutoloaders();
        $this->setConfigurations();

        $injector = new Instantiator(
            $this->getRoutingFile(),
            $this->projectRootPath
        );
        $launcher = new Launcher(
            $injector,
            new Container(
                new Collection(),
                $injector
            )
        );
        $launcher->run();

        if (!empty($this->nameSpacesBase)) {
            $injector->setNamespacesBase($this->nameSpacesBase);
        }
    }


}