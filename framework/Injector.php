<?php

namespace Delos;

use Delos\Controller\ControllerUtils;
use Delos\Exception\Exception;
use Delos\Parser\XmlParser;
use Delos\Request\Request;
use Delos\Routing\RouterAdminXmlProvider;
use Delos\Routing\RouterXml;
use Delos\Security\Access;
use Delos\Service\TwigService;
use Delos\Subscribers\Container\Subscribers;
use Twig\Environment;

class Injector
{
    /**
     * @var Collection
     */
    private $classCollection;
    /**
     * @var string
     */
    private $projectRootPath;
    /**
     * @var string
     */
    private $routingFile;

    /**
     * @var Subscribers
     */
    private $subscribers;

    /**
     * @var string
     */
    private $nameSpaceString = "Delos\\\\";

    /**
     * Injector constructor.
     * @param Collection $classCollection
     * @param $routingFile
     * @param $projectRootPath
     */
    public function __construct(Collection $classCollection, $routingFile, $projectRootPath)
    {
        $this->classCollection = $classCollection;
        $this->routingFile = $projectRootPath . $routingFile;
        $this->projectRootPath = $projectRootPath;
    }

    /**
     * @param array $namespaces
     */
    public function setNamespacesBase(array $namespaces){
        $this->nameSpaceString = "";
        foreach ($namespaces as $namespace){
            $this->nameSpaceString .= $namespace."|";
        }
        $this->nameSpaceString = substr($this->nameSpaceString, 0, -1);
    }

    /**
     * @return string
     */
    public function getProjectFolder()
    {
        return $this->projectRootPath;
    }

    /**
     * @param RouterXml $router
     * @param Request $request
     * @return Environment
     * @throws \Twig\Error\LoaderError
     */
    public function getTwig(RouterXml $router, Request $request)
    {
        $service = new TwigService($this->classCollection,$router, $request);
        $twigEnvironment = $service->build($this->projectRootPath);

        $this->classCollection->set(Environment::class, $twigEnvironment);
        return $twigEnvironment;
    }

    /**
     * @param Request $request
     * @return RouterXml
     */
    public function getRouter(Request $request)
    {
        $httpRouteProviderXml = new RouterAdminXmlProvider($this->getXmlParser($this->routingFile));
        $router = new RouterXml(
            $request,
            $httpRouteProviderXml
        );

        $this->classCollection->set(RouterXml::class, $router);
        return $router;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        $request = Request::createFromGlobals();
        $this->classCollection->set(Request::class, $request);
        return $request;

    }

    /**
     * @param $routing
     * @return XmlParser
     */
    private function getXmlParser($routing)
    {
        $parser = new XmlParser($routing);

        return $parser;
    }

    /**
     * @return void
     */
    public function getAccess()
    {
        $this->classCollection->set(Access::class,new Access());
    }

    /**
     * @param Container $container
     * @return void
     */
    public function getControllerUtils(Container $container)
    {
        $this->classCollection->set(ControllerUtils::class,new ControllerUtils($container));
    }

    /**
     * @param $service
     * @return void
     * @throws Exception
     */
    public function classInjection($service)
    {
        if ($this->classCollection->containsKey($service)) {
            return $this->classCollection->get($service);
        }

        if($service == Access::class){
            $this->getAccess();
        }
        if(class_exists($service) || interface_exists($service)){
            try {
                $reflection = new \ReflectionClass($service);
            } catch (\ReflectionException $e) {
                echo "Could not get a reflection of the class $service. Error: {$e->getMessage()}";
            }

            if (!empty($reflection->getConstructor()) && !empty($reflection->getConstructor()->getParameters())) {
                /**
                 * Here we will instantiate all the complicated objects that have parameters, the code for now handles
                 * Models, Services, standard object to be used (Request, Router)
                 * If we want to list handle objects that have parameters in their constructor we will could list what the
                 * objects needs in a xml or yml.
                 */
                $parametersArray = array();
                foreach ($reflection->getConstructor()->getParameters() as $param) {
                    /**
                     * This injector converts the models that have in their constructor a db manager with a connection
                     * they have to explicitly indicate the connection used
                     */
                    try{
                        if(empty($param->getClass())){
                            throw new Exception("Error: The parameter $param does not have a type! in the object: $service");
                        }
                    }catch (Exception $exception){
                        echo $exception->getMessageHtml($this->getProjectFolder());
                    }
                    $paramClassName = $this->getConcretionFromInterfaceName($param,$reflection->getConstructor()->getDocComment());
                    preg_match("/".$this->nameSpaceString."/", $paramClassName, $matches);
                    if (!empty($matches)) {
                        $this->classInjection($paramClassName);
                    }
                    $parametersArray[] = $this->classCollection->get($param->getClass()->getName());
                }
                $this->classCollection->set($service, new $service(...$parametersArray));

            } else if (!$this->classCollection->containsKey($service)) {
                /**
                 * Here we are dealing with simple object that requires no arguments.
                 * We have to make sure there is instance because of possible loops the container is not controlling
                 */
                $this->classCollection->set($service, new $service());
            }
        }
    }

    /**
     * @param $param
     * @param $DocComment
     * @return mixed
     */
    public function getConcretionFromInterfaceName($param,$DocComment)
    {
        /** @var \ReflectionParameter $param */
        $name = $param->getClass()->getName();
        try {
            if (interface_exists($name) || $param->getClass()->isAbstract()) {
                $nameInterface = $name;
                preg_match("#[a-zA-Z]*$#", $nameInterface, $nameMatches);
                if (empty($nameMatches[0])) {
                    throw new Exception("The interface '$nameInterface' could not be found!  \n" . __FILE__ . ' line:' . __LINE__ . " </br></br>");
                } else {
                    $interfacePartialName = $nameMatches[0];
                }
                preg_match("#@param .*$interfacePartialName .* @concretion (.*)#", $DocComment, $matches);
                if (empty($matches[1])) {
                    throw new Exception("The @concretion for the interface '$name' could not be found in the methods annotation. Please complete @concretion [ClassName] in the php doc
                      \n" . __FILE__ . ' line:' . __LINE__ . " </br></br>");
                }
                return $matches[1];
            }
            return $name;
        }catch (Exception $exception){
            echo $exception->getMessageHtml($this->getProjectFolder());
        }
    }
}