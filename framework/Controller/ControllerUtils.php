<?php

namespace Delos\Controller;

use Delos\Container;
use Delos\Request\Request;
use Delos\Response\Response;
use Delos\Response\ResponseJson;

class ControllerUtils
{
    /**
     * @var Container
     */
    private $mainContainer;

    /**
     * Controller_ControllerUtils constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->mainContainer = $container;
    }

    /**
     * @return \Delos\Routing\RouterXml
     */
    public function getRouter()
    {
        return $this->mainContainer->getRouter();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->mainContainer->getRequest();
    }

    /**
     * @return \Twig_Environment
     * @throws \Twig_Error_Loader
     */
    public function getTwig()
    {
        return $this->mainContainer->getTwig();
    }

    /**
     * @param $service
     * @return mixed
     * @throws \Delos\Exception\Exception
     */
    public function getService($service)
    {
        return $this->mainContainer->getService($service);
    }

    /**
     * @param $service
     * @return bool
     */
    public function isServiceSet($service)
    {
        return $this->mainContainer->isServiceSet($service);
    }

    /**
     * @param $service
     * @param $instance
     */
    public function setService($service,$instance)
    {
        $this->mainContainer->setService($service,$instance);
    }

    /**
     * @param $template
     * @param $parameters
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     */
    public function render($template, $parameters)
    {
        return new Response($this->mainContainer->getTwig()->render($template, $parameters));
    }

    /**
     * @param $content
     * @return ResponseJson
     */
    public function renderJson($content)
    {
        return new ResponseJson($content);
    }

    /**
     * @param $template
     * @param $variables
     * @return mixed
     * @throws \Delos\Exception\Exception
     */
    public function renderComponent($template,$variables)
    {
        $service = $this->getService(\Twig_Environment::class);
        return $service->render($template,$variables);
    }

    /**
     * @return string
     */
    public function getProjectRoot(){
        return $this->mainContainer->getProjectRoot();
    }
}