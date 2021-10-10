<?php
declare(strict_types=1);

namespace Delos;

use Delos\Controller\ControllerUtils;
use Delos\Controller\ControllerUtilsInterface;
use Delos\Exception\Exception;
use Delos\Repository\RepositoryInterface;
use Delos\Request\Request;
use Delos\Response\ResponseInterface;
use Delos\Routing\RouterXml;
use Delos\Security\Access;
use Delos\Service\ServiceInterface;
use Delos\Subscribers\Container\Subscribers;
use Twig\Environment;

class Container
{
    public Collection $classCollection;
    public Injector $injector;

    public function __construct(Collection $classCollection, Injector $injector)
    {
        $this->classCollection = $classCollection;
        $this->injector = $injector;
    }

    private function launchDelosSubscribers(): void
    {
        $subscribers = Subscribers::getDelosSubscribers();
        if (!empty($subscribers)) {
            foreach ($subscribers as $s) {
                $this->injector->classInjection($s);
            }
        }
    }

    private function launchApplicationSubscriber(): void
    {
        $subscribers = Subscribers::getApplicationSubscribers();
        if (!empty($subscribers)) {
            foreach ($subscribers as $s) {
                $this->injector->classInjection($s);
            }
        }

    }

    public function getTwig(): Environment
    {
        if ($this->classCollection->containsKey(Environment::class)) {
            return $this->classCollection->get(Environment::class);
        }

        $this->injector->getTwig($this->getRouter(), $this->getRequest());

        return $this->classCollection->get(Environment::class);
    }

    public function getRouter(): RouterXml
    {
        if ($this->classCollection->containsKey(RouterXml::class)) {
            return $this->classCollection->get(RouterXml::class);
        }

        $this->injector->getRouter($this->getRequest());

        return $this->classCollection->get(RouterXml::class);
    }

    public function getRequest(): Request
    {
        if ($this->classCollection->containsKey(Request::class)) {
            return $this->classCollection->get(Request::class);
        }

        $this->injector->getRequest();

        return $this->classCollection->get(Request::class);
    }

    public function getAccessChecker(): Access
    {
        if ($this->classCollection->containsKey(Access::class)) {
            return $this->classCollection->get(Access::class);
        }

        $this->injector->getAccess();

        return $this->classCollection->get(Access::class);
    }

    public function getControllerUtils(): ControllerUtils
    {
        if ($this->classCollection->containsKey(ControllerUtils::class)) {
            return $this->classCollection->get(ControllerUtils::class);
        }

        $this->injector->getControllerUtils($this);

        return $this->classCollection->get(ControllerUtils::class);
    }

    public function run(): void
    {
        $this->classCollection->set(Container::class, $this);
        try {
            $this->getRequest();
            $router = $this->getRouter();

            $this->launchDelosSubscribers();
            $this->launchApplicationSubscriber();

            $controller = $router->getController();
            $method = $router->getMethod();
            $access = $router->getAccess();

            $accessChecker = $this->getAccessChecker();
            $accessChecker->control($access);
        } catch (Exception $exception) {
            echo $exception->getMessageHtml($this->injector->getProjectFolder());
        }
        $reflectionClass = new \ReflectionClass($controller);
        $instances = array();
        if (empty($reflectionClass->getConstructor()) || empty($reflectionClass->getConstructor()->getParameters())) {
            $controllerInstance = new $controller();
        } else {
            foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
                $className = $this->injector->getConcretionFromInterfaceName($parameter,$reflectionClass->getConstructor()->getDocComment());
                $instances[] = $this->getService($className);
            }
            $controllerInstance = new $controller(...$instances);
        }

        $reflectionMethod = new \ReflectionMethod($controller, $method);
        $instances = array();
        foreach ($reflectionMethod->getParameters() as $p) {
            $className = $this->injector->getConcretionFromInterfaceName($p,$reflectionMethod->getDocComment());
            $instances[] = $this->getService($className);
        }

        try {
            $response = $controllerInstance->$method(...$instances);
            if (empty($response) || !($response instanceof ResponseInterface)) {
                throw new Exception("The controller needs to return an object implementing the Delos\Response\ResponseInterface.
                The controller: '$controller' with the method '$method' . Does not do that!  \n" . __FILE__ . ' line:' . __LINE__ . " </br></br>
                Hint: the method in your controller may not return need the statement 'return'");
            }
        } catch (Exception $exception) {
            echo $exception->getMessageHtml($this->injector->getProjectFolder());
        }
        /** @var ResponseInterface $response */
        $response->process();
    }

    public function getService($service):
    ServiceInterface|ControllerUtilsInterface|RepositoryInterface
    {
        if ($this->classCollection->containsKey($service)) {
            return $this->classCollection->get($service);
        }
        if ($service == ControllerUtils::class) {
            $this->getControllerUtils();
        }
        $this->injector->classInjection($service);
        return $this->classCollection->get($service);
    }

    public function isServiceSet(string $service): bool
    {
        if ($this->classCollection->containsKey($service)) {
            return true;
        }
        return false;
    }

    public function setService(string $service, ServiceInterface $instance): void
    {
        if (!$this->classCollection->containsKey($service)) {
            $this->classCollection->set($service, $instance);
        }
    }

    public function getProjectRoot(): string
    {
        return $this->injector->getProjectFolder();
    }
}