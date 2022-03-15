<?php
declare(strict_types=1);

namespace Tests\Integration\Base\DependencyInjection\TestClasses;

class ClassThree
{
    private ClassOne $classOne;
    private ClassTwo $classTwo;

    public function __construct(ClassOne $classOne, ClassTwo $classTwo)
    {
        $this->classOne = $classOne;
        $this->classTwo = $classTwo;
    }
}