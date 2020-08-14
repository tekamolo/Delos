<?php

namespace Delos;

use Delos\Database\Connection;

class Kernel
{
    /**
     * @var string
     */
    private $projectFolder;

    /**
     * @var string
     */
    private $routingFile;

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
     * @param string $projectFolder
     */
    public function setProjectFolder($projectFolder)
    {
        $this->projectFolder = $projectFolder;
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
        new Connection(); //booting eloquent
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
        $injector = new Injector($classCollection,$this->getRoutingFile(),$this->projectFolder);
        $container = new Container($classCollection,$injector);
        $container->run();
    }
}