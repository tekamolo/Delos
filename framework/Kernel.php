<?php

namespace Delos;

class Kernel
{
    /**
     * @var string
     */
    private $projectFolder;

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
        /**
         * All the constant definitions should go here
         */
    }

    private function loadAutoloaders(){
        /**
         * All the autoloads should go here
         */
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
        $injector = new Injector($classCollection,'/framework/routing.xml',$this->projectFolder);
        $container = new Container($classCollection,$injector);
        $container->run();
    }
}