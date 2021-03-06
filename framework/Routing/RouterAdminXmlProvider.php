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
     * @var
     */
    public $selectedNode;

    /**
     * @var array
     */
    public $nodes;

    /**
     * @var string
     */
    public $language;

    static $languagesArrayWithPrefix = [
        "es",
        "fr",
    ];

    /**
     * Http_Routing_RouterProviderAdminXml constructor.
     * @param XmlParser $parser
     */
    public function __construct(XmlParser $parser)
    {
        $this->xmlParser = $parser;
    }

    /**
     * @param array $requestArray
     * @param $url
     * @return array
     */
    public function getRouteByRequest(array $requestArray,$url){
        $params = [];
        $requestArray = array_reverse($requestArray);
        $this->language = "en";
        if(empty($url)){
            $url = "/";
        }elseif(preg_match("/^\//", $url) == 0){
            $url = "/" . $url;
        }
        $i=0;
        if(!empty($requestArray)){
            foreach ($requestArray as $r){
                if($url == "///" || $url == "//") $url = "/";
                $this->matchPathVar($url);
                $i++;
                if(!empty($this->selectedNode)){
                    break;
                }else{
                    $url = preg_replace("#$r(/)?$#","",$url);
                    $params[] = $r;
                }
            }
        }
        if(empty($this->selectedNode)){
            $url = "/";
            $this->matchPathVar($url);
            $params = $requestArray;
        }
        return array($url,array_reverse($params),$this->language);
    }

    /**
     * @param $pathVar
     */
    private function matchPathVar($pathVar){
        if(empty($this->nodes))
            $this->nodes = $this->xmlParser->getXpath("/routes")[0];
        foreach ($this->nodes as $n){
            foreach ($n->url as $url)
            {
                if ($pathVar == $url->__toString()){
                    $this->selectedNode = $n;
                    $this->language = $url->attributes()['lang']->__toString();
                }
            }
        }
    }

    /**
     * @param $pathName
     * @return \SimpleXMLElement[]
     */
    public function getRoute($pathName)
    {
        $result = $this->xmlParser->getXpath('/routes/route[@alias="' . $pathName . '"]');
        if (!empty($result)) {
            return $result;
        }
    }

    /**
     * @param $url
     * @param $language
     * @return \SimpleXMLElement
     * @throws Exception
     */
    private function getRouteNodeByUrl($url,$language)
    {
        $node = $this->xmlParser->searchNodeByChildrenTagValue("url[@lang='".$language."']", $url);
        if (empty($node)) {
            throw new Exception("There is no node with the locator: $url");
        }
        return $node;
    }

    /**
     * @return string
     */
    public function getBaseControllerNamespace(){
        $result = $this->xmlParser->getXpath('/routes/@namespaceBaseController');
        if (empty($result)) {
            return "Delos\\Controller\\";
        }
        return $result[0]->__toString();
    }

    /**
     * @param $url
     * @return string
     * @throws Exception
     */
    public function getControllerByUrl($url,$language)
    {
        $node = $this->getRouteNodeByUrl($url,$language);
        $controllerExplode = explode(":", $node[0]->controller);
        $controller = $this->getBaseControllerNamespace() . $controllerExplode[0];

        if (!class_exists($controller)) {
            throw new Exception("The class $controller does not exist! \n</br>".__FILE__.' line:'.__LINE__." </br></br>
                                Hints: You may have forgotten to set the extension '.php' to your controller");
        }
        return $controller;
    }

    /**
     * @param $url
     * @param $language
     * @return mixed|string
     * @throws Exception
     */
    public function getMethodByUrl($url,$language)
    {
        $node = $this->getRouteNodeByUrl($url,$language);
        $controllerExplode = explode(":", $node[0]->controller);
        $controller = $this->getBaseControllerNamespace() . $controllerExplode[0];
        if (!class_exists($controller)) {
            $controller = $controllerExplode[0];
        }

        $method = $controllerExplode[1];

        if (!method_exists($controller, $method)) {
            throw new Exception("The method '$method' inside $controller does not exist!  \n".__FILE__.' line:'.__LINE__." </br></br>");
        }
        return $method;
    }

    /**
     * @param $url
     * @param $language
     * @return string
     * @throws Exception
     */
    public function getAccessByUrl($url,$language){
        $node = $this->getRouteNodeByUrl($url,$language);
        $access = $node[0]->access->__toString();
        if (empty($access)) {
            throw new Exception("There is no security access for the locator: $url");
        }
        return $access;
    }
}