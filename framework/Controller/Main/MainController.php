<?php

namespace Delos\Controller\Main;

use Delos\Controller\ControllerUtils;
use Delos\Model\User;
use Delos\Repository\UserRepository;
use Faker\Factory;

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
     * @param UserRepository $repository
     * @return \Delos\Response\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     */
    public function mainMethod(UserRepository $repository){

        $users = $repository->getAll();

        return $this->utils->render("/main/index.html.twig",
            array("users"=> $users)
        );
    }

    /**
     * @param UserRepository $repository
     */
    public function userCreation(UserRepository $repository){
        $faker = Factory::create();
        $user = new User();
        $user->username = $faker->userName;
        $user->email = $faker->email;
        $user->password = $faker->password;
        $repository->createUser($user);

        var_dump("created");
        die();
    }
}