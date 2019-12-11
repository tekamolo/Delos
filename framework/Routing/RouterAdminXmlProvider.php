<?php

namespace Delos\Routing;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;

class RouterAdminXmlProvider
{
    /**
     * @var XmlParser
     */
    public $xmlParser;

    /**
     * Http_Routing_RouterProviderAdminXml constructor.
     * @param XmlParser $parser
     * @param $adminFolder
     */
    public function __construct(XmlParser $parser)
    {
        $this->xmlParser = $parser;
    }

    /**
     * @param $pathName
     * @return string
     */
    public function getRoute($pathName)
    {
        $result = $this->xmlParser->getXpath('/routes/route[@alias="' . $pathName . '"]');
        if (!empty($result)) {
//            return "/" . $this->adminFolder . "/" . $result[0]->url->__toString() . "/";
        }
    }

    /**
     * @param $url
     * @return \SimpleXMLElement
     * @throws Exception
     */
    private function getRouteNodeByUrl($url)
    {
        $node = $this->xmlParser->searchNodeByChildrenTagValue("url", $url);
        if (empty($node)) {
            throw new Exception("There is no node with the locator: $url or $url.php");
        }
        return $node;
    }

    /**
     * @param $url
     * @return string
     * @throws Exception
     */
    public function getControllerByUrl($url)
    {
        $node = $this->getRouteNodeByUrl($url);
        $controllerExplode = explode(":", $node[0]->controller);
        $controller = "Delos\\Controller\\" . $controllerExplode[0];
        if (!class_exists($controller)) {
            throw new Exception("The class $controller does not exist! \n</br>".__FILE__.' line:'.__LINE__." </br></br>
                                Hints: You may have forgotten to set the extension '.php' to your controller");
        }
        return $controller;
    }

    /**
     * @param $url
     * @return string
     * @throws Exception
     */
    public function getMethodByUrl($url)
    {
        $node = $this->getRouteNodeByUrl($url);
        $controllerExplode = explode(":", $node[0]->controller);
        $controller = "Delos\\Controller\\" . $controllerExplode[0];
        $method = $controllerExplode[1];
        if (!method_exists($controller, $method)) {
            throw new Exception("The method '$method' inside $controller does not exist!  \n".__FILE__.' line:'.__LINE__." </br></br>");
        }
        return $method;
    }

    /**
     * @param $url
     * @return string
     * @throws Exception
     */
    public function getAccessByUrl($url){
        $node = $this->getRouteNodeByUrl($url);
        $access = $node[0]->access;
        if (empty($access)) {
            throw new Exception("There is no security access for the locator: $url");
        }
        return $access;
    }
}