<?php

namespace Delos\Service;

use Delos\Collection;
use Delos\Container;
use Delos\Exception\Exception;
use Delos\Extension\TwigExtensionStatic;
use Delos\Request\Request;
use Delos\Routing\RouterXml;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigService
{
    /**
     * @var Collection
     */
    public $collection;
    /**
     * @var Environment
     */
    public $instance;
    /**
     * @var RouterXml
     */
    public $router;

    /**
     * @var string
     */
    public $projectRootPath;

    /**
     * @var Request
     */
    public $request;

    public function __construct(Collection $collection,RouterXml $router, Request $request)
    {
        $this->collection = $collection;
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @param $projectRootPath
     * @return Environment
     * @throws LoaderError
     */
    public function build($projectRootPath)
    {
        $this->projectRootPath = $projectRootPath;
        $loader = new FilesystemLoader($projectRootPath . '/views');
        $loader->addPath($projectRootPath . '/views/main');
        $twig = new Environment($loader);
        $twig->enableDebug();
        $twig->addExtension(new DebugExtension());
        $twig->addGlobal('router', $this->router);
        $twig->addGlobal('request', $this->request);
        $twig->addGlobal('component',$this);
        $twig->addExtension(new TwigExtensionStatic());
        $this->instance = $twig;
        return $twig;
    }

    /**
     * @param $controller
     * @param $method
     * @param array $parameters
     * @return mixed
     * @throws Exception
     * @throws ReflectionException
     */
    public function render($controller,$method,$parameters=array())
    {
        /** @var Container $container */
        $container = $this->collection->get(Container::class);
        $controller = "Delos\\Controller\\".$controller;

        $controllerInstance = $this->collection->get($controller);
        if(empty($controllerInstance)){
            $reflectionClass = new ReflectionClass($controller);
            $instances = array();
            if(empty($reflectionClass->getConstructor()) || empty($reflectionClass->getConstructor()->getParameters())){
                $controllerInstance = new $controller();
            }else{
                foreach ($reflectionClass->getConstructor()->getParameters() as $parameter){
                    $instances[] = $container->getService($parameter->getClass()->name);
                }
                $controllerInstance = new $controller(...$instances);
            }
        }
        if(method_exists($controller,"setParameters")){
            $controllerInstance->setParameters($parameters);
        }

        $reflectionMethod = new ReflectionMethod($controller,$method);
        $instances = array();
        foreach ($reflectionMethod->getParameters() as $p){
            $instances[] = $container->getService($p->getClass()->name);
        }

        try{
            $response = $controllerInstance->$method(...$instances);
            if(empty($response)){
                throw new Exception("The controller needs to return an object implementing the Delos\Response\ResponseInterface.
                The controller: '$controller' with the method '$method' . Does not do that!  \n".__FILE__.' line:'.__LINE__);
            }
        }catch (Exception $exception){
            echo $exception->getMessageHtml($this->projectRootPath);
        }
        /** @var $response */
        return $response;
    }
}