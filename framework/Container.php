<?php
declare(strict_types=1);

namespace Delos;

use Delos\Controller\ControllerUtils;
use Delos\Parser\XmlParser;
use Delos\Request\Request;
use Delos\Routing\RouterXml;
use Delos\Security\Access;
use Twig\Environment;

final class Container
{
    public Collection $classCollection;
    public Instantiator $instantiator;

    public function __construct(
        Collection   $classCollection,
        Instantiator $instantiator
    )
    {
        $this->classCollection = $classCollection;
        $this->instantiator = $instantiator;
    }

    public function getTwig(): Environment
    {
        if ($this->classCollection->containsKey(Environment::class)) {
            return $this->classCollection->get(Environment::class);
        }

        $twigEnvironment = $this->instantiator->instantiateTwigEnvironment($this->classCollection, $this);
        $this->classCollection->set(Environment::class, $twigEnvironment);
        return $this->classCollection->get(Environment::class);
    }

    public function getRouter(): RouterXml
    {
        if ($this->classCollection->containsKey(RouterXml::class)) {
            return $this->classCollection->get(RouterXml::class);
        }

        $router = $this->instantiator->getRouter($this->getRequest());
        $this->classCollection->set(RouterXml::class, $router);

        return $router;
    }

    public function getRequest(): Request
    {
        return $this->getInternalObject(Request::class, "getRequest");
    }

    public function getXmlParser(): XmlParser
    {
        return $this->getInternalObject(XmlParser::class, "getXmlParser");
    }

    public function getAccessChecker(): Access
    {
        return $this->getInternalObject(Access::class, "getAccess");
    }

    public function getControllerUtils(): ControllerUtils
    {
        return $this->getInternalObject(ControllerUtils::class, "getControllerUtils");
    }

    private function getInternalObject(string $object, string $method)
    {
        if ($this->classCollection->containsKey($object)) {
            return $this->classCollection->get($object);
        }

        $controlUtils = $this->instantiator->$method($this);
        $this->classCollection->set($object, $controlUtils);

        return $this->classCollection->get($object);
    }

    public function getService($service): object
    {
        if ($this->classCollection->containsKey($service)) {
            return $this->classCollection->get($service);
        }

        $serviceInstance = $this->instantiator->classInjection($this, $service);
        $this->classCollection->set($service, $serviceInstance);

        return $serviceInstance;
    }

    public function isServiceSet(string $service): bool
    {
        if ($this->classCollection->containsKey($service)) {
            return true;
        }
        return false;
    }

    public function setService(string $service, $instance): void
    {
        if (!$this->classCollection->containsKey($service)) {
            $this->classCollection->set($service, $instance);
        }
    }

    public function getProjectRoot(): string
    {
        return $this->instantiator->getProjectFolder();
    }
}