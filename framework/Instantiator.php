<?php
declare(strict_types=1);

namespace Delos;

use Delos\Controller\ControllerUtils;
use Delos\Exception\Exception;
use Delos\Parser\XmlParser;
use Delos\Request\Request;
use Delos\Routing\RouterAdminXmlProvider;
use Delos\Routing\RouterXml;
use Delos\Security\Access;

final class Instantiator
{
    private string $projectRootPath;
    private string $routingFile;
    private string $nameSpaceString = "Delos\\\\";

    /**
     * @param $routingFile
     * @param $projectRootPath
     */
    public function __construct($routingFile, $projectRootPath)
    {
        $this->routingFile = $projectRootPath . $routingFile;
        $this->projectRootPath = $projectRootPath;
    }

    public function setNamespacesBase(array $namespaces): void
    {
        $this->nameSpaceString = "";
        foreach ($namespaces as $namespace) {
            $this->nameSpaceString .= $namespace . "|";
        }
        $this->nameSpaceString = substr($this->nameSpaceString, 0, -1);
    }

    public function getProjectFolder(): string
    {
        return $this->projectRootPath;
    }

    public function getRouter(Request $request): RouterXml
    {
        $httpRouteProviderXml = new RouterAdminXmlProvider($this->getXmlParser($this->routingFile));
        return new RouterXml(
            $request,
            $httpRouteProviderXml
        );
    }

    public function getRequest(): Request
    {
        return Request::createFromGlobals();

    }

    private function getXmlParser(string $routing): XmlParser
    {
        return new XmlParser($routing);
    }

    public function getAccess(): Access
    {
        return new Access();
    }

    public function getControllerUtils(Container $container): ControllerUtils
    {
        return new ControllerUtils($container);
    }

    public function classInjection(Container $container, string $service): ?object
    {
        if (class_exists($service) || interface_exists($service)) {
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
                        if (empty($param->getType())) {
                            throw new Exception("Error: The parameter $param does not have a type! in the object: $service");
                        }
                    }catch (Exception $exception){
                        echo $exception->getMessageHtml($this->getProjectFolder());
                    }
                    $paramClassName = $this->getConcretionFromInterfaceName($param, $reflection->getConstructor()->getDocComment());
                    preg_match("/" . $this->nameSpaceString . "/", $paramClassName, $matches);
                    if (!empty($matches)) {
                        $parametersArray[] = $this->classInjection($container, $paramClassName);
                    } else {
                        $parametersArray[] = $container->getService($param->getType()->getName());
                    }
                }
                return new $service(...$parametersArray);

            } else if (!$container->isServiceSet($service)) {
                /**
                 * Here we are dealing with simple object that requires no arguments.
                 * We have to make sure there is instance because of possible loops the container is not controlling
                 */
                return new $service();
            }
        }
        return null;
    }

    /**
     * @param $param
     * @param $DocComment
     * @return mixed
     */
    public function getConcretionFromInterfaceName($param, $DocComment): string
    {
        /** @var \ReflectionParameter $param */
        $name = $param->getType()->getName();
        try {
            if (interface_exists($name) || $this->getClassFromParameter($param)->isAbstract()) {
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

        } catch (Exception $exception) {
            echo $exception->getMessageHtml($this->getProjectFolder());
        }
        return $name;
    }

    private function getClassFromParameter(\ReflectionParameter $param)
    {
        return $param->getType() && !$param->getType()->isBuiltin()
            ? new \ReflectionClass($param->getType()->getName())
            : null;
    }
}