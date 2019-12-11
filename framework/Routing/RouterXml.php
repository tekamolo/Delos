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
     * Http_Routing_RouterXml constructor.
     * @param Request $request
     * @param RouterAdminXmlProvider $providerAdminXml
     */
    public function __construct(Request $request, RouterAdminXmlProvider $providerAdminXml)
    {
        $this->request = $request;
        $this->xmlRouteProvider = $providerAdminXml;

//        var_dump($this->request->get->getRawData());

        $this->processUrl($this->request->get->getRawData());
    }

    /**
     * @param $url
     */
    public function processUrl($url)
    {
        /** Gets the url and separate it between the url and the parameters */
        preg_match_all("/([\w-]+)/", $url['url'], $urlMatches);
        if(empty($urlMatches[0])) $urlMatches[0] = [""];

        $this->url = array_shift($urlMatches[0]);
        $this->parameters = $urlMatches[0];
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

    public function getFullCurrentUrl()
    {
        $params = "";
        if(!empty($this->parameters)){
            foreach ($this->parameters as $p){
                $params .= "$p/";
            }
        }
        return "/".$this->url."/".$params;
    }

    /**
     * Shorthand of getUrlFromPathname() method
     * @param string $pathName This is actually the alias
     * @return string
     * @throws Exception
     */
    public function getUrl($pathName)
    {
        return $this->getUrlFromXmlAndProvider($pathName);
    }

    /**
     * Search for the Url in the xml routing file if nothing is found it searches inside the original provider.
     * @param $pathName
     * @return string
     * @throws Exception
     */
    private function getUrlFromXmlAndProvider($pathName)
    {
        $result = $this->xmlRouteProvider->getRoute($pathName);
        if (!empty($result)) {
            return $result;
        } else if (!empty($this->getUrlFromPathname($pathName))) {
            return $this->getUrlFromPathname($pathName);
        }
        throw new Exception("There is no route with the alias: $pathName");
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getController()
    {
        return $this->xmlRouteProvider->getControllerByUrl($this->url);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getMethod()
    {
        return $this->xmlRouteProvider->getMethodByUrl($this->url);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAccess()
    {
        return $this->xmlRouteProvider->getAccessByUrl($this->url);
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
     * @param $pathName
     * @return string
     */
    public function getUrlFromPathname($pathName)
    {
        $allRoutes = $this->xmlRouteProvider->getRoutes();

        $routeExists = array_key_exists($pathName, $allRoutes);

        if ($routeExists === false) {
            return 'Unable to find route for the pathname ' . $pathName;
        }

        $url = $allRoutes[$pathName];

        return $url;
    }
}