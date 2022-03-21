<?php
declare(strict_types=1);

namespace Tests\Integration\Base\DependencyInjection\TestClasses;

class ClassFive
{
    private InterfaceOne $classOne;
    private ClassTwo $classTwo;
    private InterfaceFour $classFour;

    /**
     * @param InterfaceOne $classOne @concretion Tests\Integration\Base\DependencyInjection\TestClasses\ClassOne
     * @param InterfaceFour $classFour @concretion Tests\Integration\Base\DependencyInjection\TestClasses\ClassFour
     */
    public function __construct(InterfaceOne $classOne, ClassTwo $classTwo, InterfaceFour $classFour)
    {
        $this->classOne = $classOne;
        $this->classTwo = $classTwo;
        $this->classFour = $classFour;
    }
}