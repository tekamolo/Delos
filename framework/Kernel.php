<?php
declare(strict_types=1);

namespace Delos;

use Delos\Database\Connection;
use Delos\Shared\Directory;
use Delos\Shared\File;

final class Kernel
{
    private Directory $projectRootPath;
    private File $routingFile;
    private File $environmentFile;
    private array $nameSpacesBase;
    private Container $container;
    private Instantiator $instantiator;
    private Launcher $launcher;

    public function setRoutingFile(File $routingFile): void
    {
        $this->routingFile = $routingFile;
    }

    public function getEnvironmentFile(): File
    {
        return $this->environmentFile;
    }

    public function setEnvironmentFile(File $environmentFile): void
    {
        $this->environmentFile = $environmentFile;
    }

    public function setProjectRootPath(Directory $projectRootPath)
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
        if (file_exists($this->projectRootPath->getPath() . '/composer.json')) {
            $content = file_get_contents($this->projectRootPath->getPath() . '/composer.json');
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

        $this->instantiator = new Instantiator(
            $this->routingFile,
            $this->projectRootPath
        );
        $this->container = $this->getContainer($this->instantiator);

        $this->launcher = new Launcher(
            $this->instantiator,
            $this->container
        );
        $this->launcher->run();

        if (!empty($this->nameSpacesBase)) {
            $this->instantiator->setNamespacesBase($this->nameSpacesBase);
        }
    }

    public function getContainer(Instantiator $instantiator)
    {
        if (!empty($this->container))
            return $this->container;
        return new Container(
            new Collection(),
            $this->instantiator
        );
    }


}