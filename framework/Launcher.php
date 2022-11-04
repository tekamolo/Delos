<?php
declare(strict_types=1);

namespace Delos;

use Delos\Exception\Exception;
use Delos\Exception\ExceptionToJson;
use Delos\Response\ResponseInterface;
use Delos\Response\ResponseJson;
use Delos\Subscribers\Container\Subscribers;

final class Launcher
{
    private Container $container;
    private Instantiator $instantiator;

    public function __construct(Instantiator $instantiator, Container $container)
    {
        $this->container = $container;
        $this->instantiator = $instantiator;
    }

    public function run(): void
    {
        $this->container->setService(Container::class, $this->container);
        try {
            $this->launchDelosSubscribers();
            $this->launchApplicationSubscriber();
            $this->container->getRequest();
            $router = $this->container->getRouter();

            $controller = $router->getController();
            $method = $router->getMethod();
            $access = $router->getAccess();

            $accessChecker = $this->container->getAccessChecker();
            $accessChecker->control($access);
        } catch (ExceptionToJson $exception) {
            $response = new ResponseJson(["message" => $exception->getMessage()],400);
            $response->process();
            die();
        } catch (Exception $exception) {
            echo $exception->getMessageHtml(
                $this->instantiator->getProjectFolder()->getPath()
            );
        }
        $reflectionClass = new \ReflectionClass($controller);
        $instances = array();
        if (empty($reflectionClass->getConstructor()) || empty($reflectionClass->getConstructor()->getParameters())) {
            $controllerInstance = new $controller();
        } else {
            foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
                $className = $this->instantiator->getConcretionFromInterfaceName($parameter, $reflectionClass->getConstructor()->getDocComment());
                $instances[] = $this->container->getService($className);
            }
            $controllerInstance = new $controller(...$instances);
        }

        $reflectionMethod = new \ReflectionMethod($controller, $method);
        $instances = array();
        foreach ($reflectionMethod->getParameters() as $p) {
            $className = $this->instantiator->getConcretionFromInterfaceName($p, $reflectionMethod->getDocComment());
            $instances[] = $this->container->getService($className);
        }

        try {
            $response = $controllerInstance->$method(...$instances);
            if (empty($response) || !($response instanceof ResponseInterface)) {
                throw new Exception("The controller needs to return an object implementing the Delos\Response\ResponseInterface.
                The controller: '$controller' with the method '$method' . Does not do that!  \n" . __FILE__ . ' line:' . __LINE__ . " </br></br>
                Hint: the method in your controller may not return need the statement 'return'");
            }
        } catch (Exception $exception) {
            echo $exception->getMessageHtml($this->instantiator->getProjectFolder());
        }
        /** @var ResponseInterface $response */
        $response->process();
    }

    public function launchDelosSubscribers(): void
    {
        $subscribers = Subscribers::getDelosSubscribers();
        if (!empty($subscribers)) {
            foreach ($subscribers as $s) {
                $this->instantiator->classInjection($this->container, $s);
            }
        }
    }

    public function launchApplicationSubscriber(): void
    {
        $subscribers = Subscribers::getApplicationSubscribers();
        if (!empty($subscribers)) {
            foreach ($subscribers as $s) {
                $this->instantiator->classInjection($this->container, $s);
            }
        }
    }
}