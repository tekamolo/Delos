<?php
declare(strict_types=1);

namespace Delos;

use Delos\Controller\ControllerUtils;
use Delos\Request\Request;
use Delos\Routing\RouterXml;
use Delos\Security\Access;
use Delos\Service\TwigService;
use Twig\Environment;

final class Container
{
    public Collection $classCollection;
    public Instantiator $instantiator;

    public function __construct(Collection $classCollection, Instantiator $instantiator)
    {
        $this->classCollection = $classCollection;
        $this->instantiator = $instantiator;
    }

    public function getTwig(): Environment
    {
        if ($this->classCollection->containsKey(Environment::class)) {
            return $this->classCollection->get(Environment::class);
        }

        $service = new TwigService($this->classCollection, $this->getRouter(), $this->getRequest());
        $twigEnvironment = $service->build($this->instantiator->getProjectFolder());

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
        if ($this->classCollection->containsKey(Request::class)) {
            return $this->classCollection->get(Request::class);
        }

        $request = $this->instantiator->getRequest();
        $this->classCollection->set(Request::class, $request);

        return $request;
    }

    public function getAccessChecker(): Access
    {
        if ($this->classCollection->containsKey(Access::class)) {
            return $this->classCollection->get(Access::class);
        }

        $access = $this->instantiator->getAccess();
        $this->classCollection->set(Access::class, $access);

        return $access;
    }

    public function getControllerUtils(): ControllerUtils
    {
        if ($this->classCollection->containsKey(ControllerUtils::class)) {
            return $this->classCollection->get(ControllerUtils::class);
        }

        $controlUtils = $this->instantiator->getControllerUtils($this);
        $this->classCollection->set(ControllerUtils::class, $controlUtils);

        return $this->classCollection->get(ControllerUtils::class);
    }

    public function getService($service): object
    {
        if ($this->classCollection->containsKey($service)) {
            return $this->classCollection->get($service);
        }

        if ($service == ControllerUtils::class) {
            return $this->getControllerUtils();
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