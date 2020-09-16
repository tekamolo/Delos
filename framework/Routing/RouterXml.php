<?php

namespace Delos\Routing;

use Delos\Exception\Exception;
use Delos\Request\Request;

class RouterXml
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var RouterAdminXmlProvider
     */
    private $xmlRouteProvider;
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $languages = array("en","fr","es");

    private $selectedLanguage = "en";

    /**
     * Http_Routing_RouterXml constructor.
     * @param Request $request
     * @param RouterAdminXmlProvider $providerAdminXml
     */
    public function __construct(Request $request, RouterAdminXmlProvider $providerAdminXml)
    {
        $this->request = $request;
        $this->xmlRouteProvider = $providerAdminXml;
        $this->processUrl($this->request->get->getRawData());
    }

    /**
     * @param $url
     */
    public function processUrl($url)
    {
        /** Gets the url and separate it between the url and the parameters */
        preg_match_all("/([\w-]+)/", $url['url'], $urlMatches);
        $matches = (!empty($urlMatches[0])) ? $urlMatches[0] : array("/");
        [$url,$params,$language] = $this->xmlRouteProvider->getRouteByRequest($matches);
        $this->url = $url;
        $this->parameters=$params;
        $this->selectedLanguage = $language;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->parameters;
    }

    public function getParam($position)
    {
        return !empty($this->parameters[$position]) ? $this->parameters[$position] : null;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getCurrentUrlWithParams()
    {
        $params = "";
        if(!empty($this->parameters)){
            foreach ($this->parameters as $p){
                $params .= "$p/";
            }
        }
        $base = $this->url;
        $base = ($base == "///" || $base == "") ? "/": $base;
        return $base.$params;
    }

    /**
     * Shorthand of getUrlFromPathname() method
     * @param string $pathName This is actually the alias
     * @param null $language
     * @return string
     * @throws Exception
     */
    public function getUrl($pathName,$language=null)
    {
        $language = !empty($language) ? $language : $this->selectedLanguage;
        return $this->getUrlFromXmlAndProvider($pathName,$language);
    }

    /**
     * @param $pathName
     * @return string
     * @throws Exception
     */
    private function getUrlFromXmlAndProvider($pathName,$language)
    {
        $nodeArray = $this->xmlRouteProvider->getRoute($pathName);
        if (!empty($nodeArray)) {
            /** @var \SimpleXMLElement $node */
            $node = $nodeArray[0];
            return (string) $node->xpath("url[@lang='".$language."']")[0];
        }
        throw new Exception("There is no route with the alias: $pathName");
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getController()
    {
        return $this->xmlRouteProvider->getControllerByUrl($this->url,$this->selectedLanguage);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getMethod()
    {
        return $this->xmlRouteProvider->getMethodByUrl($this->url,$this->selectedLanguage);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAccess()
    {
        return $this->xmlRouteProvider->getAccessByUrl($this->url,$this->selectedLanguage);
    }

    /**
     * @return string
     */
    public function getCurrentAlias(){
        /** @var \SimpleXMLElement $nodeArray */
        $nodeArray = $this->xmlRouteProvider->selectedNode;
        return $nodeArray->attributes()->alias->__toString();
    }

    /**
     * @param $pathName
     * @param array $parameters
     * @throws Exception
     */
    public function redirect($pathName,$parameters=array())
    {
        $url = $this->getUrl($pathName);
        $stringParams = $this->getGetParameters($parameters);
        header("location: $url$stringParams");
        die();
    }

    /**
     * @param $pathName
     * @param $language
     * @param array $parameters
     * @throws Exception
     */
    public function redirectByLanguage($pathName,$language,$parameters=array())
    {
        $url = $this->getUrl($pathName,$language);
        $stringParams = $this->getGetParameters($parameters);
        header("location: $url$stringParams");
        die();
    }

    /**
     * @param $pathName
     * @param array $parameters
     * @throws Exception
     */
    public function delosRedirect($pathName, $parameters = array())
    {
        $url = $this->getUrl($pathName);
        $stringParams = "";
        if (!empty($parameters)){
            foreach ($parameters as $v) {
                $stringParams .= "$v/";
            }
        }
        header("location: $url$stringParams");
        die();
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function getGetParameters(array $parameters){
        $stringParams = "";
        if(empty($parameters)) return $stringParams;
        $i = 0;
        foreach ($parameters as $k => $v) {
            $separator = ($i == 0) ? "?" : "&";
            $stringParams .= "$separator$k=$v";
            $i++;
        }
        return $stringParams;
    }

    /**
     * @return string
     */
    public function getCurrentLanguage(){
        return $this->selectedLanguage;
    }

    /**
     * @return string|null
     */
    public function getHttpHost(){
        return $this->request->server->get("HTTP_HOST");
    }
}