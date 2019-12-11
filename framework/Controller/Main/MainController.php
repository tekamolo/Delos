<?php

namespace Delos\Controller\Main;

use Delos\Controller\ControllerUtils;

class MainController
{
    /**
     * @var ControllerUtils
     */
    private $utils;

    /**
     * MainController constructor.
     * @param ControllerUtils $utils
     */
    public function __construct(ControllerUtils $utils)
    {
        $this->utils = $utils;
    }

    /**
     * @return \Delos\Response\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     */
    public function mainMethod(){
        return $this->utils->render("/main/index.html.twig",array());
    }
}