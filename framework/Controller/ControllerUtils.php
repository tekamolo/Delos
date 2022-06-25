<?php
declare(strict_types=1);

namespace Delos\Controller;

use Delos\Container;
use Delos\Request\Request;
use Delos\Response\Response;
use Delos\Response\ResponseJson;
use Delos\Routing\RouterXml;
use Delos\Service\ServiceInterface;
use Delos\Shared\Directory;
use Twig\Environment;

final class ControllerUtils implements ControllerUtilsInterface
{
    private Container $mainContainer;

    public function __construct(Container $container)
    {
        $this->mainContainer = $container;
    }

    public function getRouter(): RouterXml
    {
        return $this->mainContainer->getRouter();
    }

    public function getRequest(): Request
    {
        return $this->mainContainer->getRequest();
    }

    public function getTwig(): Environment
    {
        return $this->mainContainer->getTwig();
    }

    public function getService(string $service): ServiceInterface
    {
        return $this->mainContainer->getService($service);
    }

    public function isServiceSet(string $service): bool
    {
        return $this->mainContainer->isServiceSet($service);
    }

    public function setService(string $service, ServiceInterface $instance): void
    {
        $this->mainContainer->setService($service, $instance);
    }

    public function render(string $template, array $parameters): Response
    {
        return new Response($this->mainContainer->getTwig()->render($template, $parameters));
    }

    public function renderJson(array $content, int $http_code = 200, array $methods = ["GET"]): ResponseJson
    {
        return new ResponseJson($content, $http_code, $methods);
    }

    public function renderComponent(string $template, array $variables): string
    {
        /** @var Environment $service */
        $service = $this->getService(Environment::class);
        return $service->render($template, $variables);
    }

    public function getProjectRoot(): Directory
    {
        return $this->mainContainer->getProjectRoot();
    }
}