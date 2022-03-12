<?php
declare(strict_types=1);

namespace Delos\Controller\Main;

use Delos\Controller\ControllerUtils;
use Delos\Model\User;
use Delos\Repository\UserRepository;
use Delos\Response\Response;
use Faker\Factory;

final class MainController
{
    private ControllerUtils $utils;

    public function __construct(ControllerUtils $utils)
    {
        $this->utils = $utils;
    }

    public function mainMethod(UserRepository $repository): Response
    {
        $users = $repository->getAll();

        return $this->utils->render("/main/index.html.twig",
            array("users" => $users)
        );
    }

    public function userCreation(UserRepository $repository): void
    {
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