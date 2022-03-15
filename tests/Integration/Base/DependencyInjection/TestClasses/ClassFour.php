<?php
declare(strict_types=1);

namespace Tests\Integration\Base\DependencyInjection\TestClasses;

class ClassFour
{
    private InterfaceOne $classOne;
    private ClassTwo $classTwo;

    /**
     * @param InterfaceOne $classOne @concretion Tests\Integration\Base\DependencyInjection\TestClasses\ClassOne
     */
    public function __construct(InterfaceOne $classOne, ClassTwo $classTwo)
    {
        $this->classOne = $classOne;
        $this->classTwo = $classTwo;
    }
}