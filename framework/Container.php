<?php

namespace Delos;

use Delos\Controller\ControllerUtils;
use Delos\Exception\Exception;
use Delos\Request\Request;
use Delos\Response\ResponseInteface;
use Delos\Routing\RouterXml;
use Delos\Security\Access;
use Twig\Environment;

class Container
{
    /**
     * @var Collection
     */
    public $classCollection;
    /**
     * @var Injector
     */
    public $injector;

    /**
     * Container_Container constructor.
     * @param Collection $classCollection
     * @param Injector $injector
     */
    public function __construct(Collection $classCollection, Injector $injector)
    {
        $this->classCollection = $classCollection;
        $this->injector = $injector;
    }

    /**
     * @throws Exception
     */
    private function launchDelosSubscribers()
    {
        $subscribers = $this->injector->getDelosSubscribers();
        foreach ($subscribers as $s)
        {
             $this->injector->classInjection($s);
        }
    }

    /**
     * @throws Exception
     */
    private function launchApplicationSubscriber(){
        $subscribers = $this->injector->getApplicationSubscribers();
        if(!empty($subscribers)){
            foreach ($subscribers as $s)
            {
                $this->injector->classInjection($s);
            }
        }

    }

    /**
     * @return Environment
     * @throws \Twig\Error\LoaderError
     */
    public function getTwig()
    {
        if ($this->classCollection->containsKey(Environment::class)) {
            return $this->classCollection->get(Environment::class);
        }

        $this->injector->getTwig($this->getRouter(), $this->getRequest());

        return $this->classCollection->get(Environment::class);
    }

    /**
     * @return RouterXml
     */
    public function getRouter()
    {
        if ($this->classCollection->containsKey(RouterXml::class)) {
            return $this->classCollection->get(RouterXml::class);
        }

        $this->injector->getRouter($this->getRequest());

        return $this->classCollection->get(RouterXml::class);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if ($this->classCollection->containsKey(Request::class)) {
            return $this->classCollection->get(Request::class);
        }

        $this->injector->getRequest();

        return $this->classCollection->get(Request::class);
    }

    /**
     * @return Access
     */
    public function getAccessChecker()
    {
        if ($this->classCollection->containsKey(Access::class)) {
            return $this->classCollection->get(Access::class);
        }

        $this->injector->getAccess();

        return $this->classCollection->get(Access::class);
    }

    /**
     * @return ControllerUtils
     */
    public function getControllerUtils()
    {
        if ($this->classCollection->containsKey(ControllerUtils::class)) {
            return $this->classCollection->get(ControllerUtils::class);
        }

        $this->injector->getControllerUtils($this);

        return $this->classCollection->get(ControllerUtils::class);
    }

    /**
     * The output takes place here
     * @throws \Exception
     */
    public function run()
    {
        $this->classCollection->set(Container::class, $this);
        try {
            $this->launchDelosSubscribers();
            /** @var RouterXml $router */
            $router = $this->getRouter();

            $controller = $router->getController();
            $method = $router->getMethod();
            $access = $router->getAccess();

            /** @var Access $accessChecker */
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
            if (empty($response) || !($response instanceof ResponseInteface)) {
                throw new Exception("The controller needs to return an object implementing the Delos\Response\ResponseInterface.
                The controller: '$controller' with the method '$method' . Does not do that!  \n".__FILE__.' line:'.__LINE__." </br></br>
                Hint: the method in your controller may not return need the statement 'return'");
            }
        }catch (Exception $exception){
            echo $exception->getMessageHtml($this->injector->getProjectFolder());
        }
        /** @var ResponseInteface $response */
        $response->process();
    }

    /**
     * @param $service
     * @return mixed
     * @throws Exception
     */
    public function getService($service)
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

    /**
     * @param $service
     * @return bool
     */
    public function isServiceSet($service){
        if ($this->classCollection->containsKey($service)) {
            return true;
        }
        return false;
    }

    /**
     * @param $service
     * @param $instance
     */
    public function setService($service,$instance)
    {
        if (!$this->classCollection->containsKey($service)) {
            $this->classCollection->set($service,$instance);
        }
    }

    /**
     * @return string
     */
    public function getProjectRoot(){
        return $this->injector->getProjectFolder();
    }
}