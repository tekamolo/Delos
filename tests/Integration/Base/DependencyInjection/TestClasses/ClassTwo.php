<?php
declare(strict_types=1);

namespace Tests\Integration\Base\DependencyInjection\TestClasses;

class ClassTwo
{
    private ClassOne $classOne;

    public function __construct(ClassOne $classOne)
    {
        $this->classOne = $classOne;
    }
}