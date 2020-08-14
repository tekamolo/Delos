<?php

namespace Delos;

use Delos\Database\Connection;

class Kernel
{
    /**
     * @var string
     */
    private $projectRootPath;

    /**
     * @var string
     */
    private $routingFile;

    /**
     * @var string
     */
    private $environmentFile;

    /**
     * @return string
     */
    public function getRoutingFile(): string
    {
        return $this->routingFile;
    }

    /**
     * @param string $routingFile
     */
    public function setRoutingFile(string $routingFile): void
    {
        $this->routingFile = $routingFile;
    }

    /**
     * @return string
     */
    public function getEnvironmentFile(): string
    {
        return $this->environmentFile;
    }

    /**
     * @param string $environmentFile
     */
    public function setEnvironmentFile(string $environmentFile): void
    {
        $this->environmentFile = $environmentFile;
    }

    /**
     * @param string $projectRootPath
     */
    public function setProjectRootPath($projectRootPath)
    {
        $this->projectRootPath = $projectRootPath;
    }

    /**
     * Kernel constructor.
     */
    public function __construct()
    {
    }

    private function setConstants(){
    }

    private function loadAutoloaders(){
        new Connection($this->environmentFile); //booting eloquent
    }

    private function setConfigurations(){
        /**
         * Configurations should go here, whether they are developing, test or production
         */
    }

    /**
     * @throws \Exception
     */
    public function boot()
    {
        $this->setConstants();
        $this->loadAutoloaders();
        $this->setConfigurations();

        $classCollection = new Collection();
        $injector = new Injector($classCollection,$this->getRoutingFile(),$this->projectRootPath);
        $container = new Container($classCollection,$injector);
        $container->run();
    }
}