<?php
declare(strict_types=1);

namespace Tests\Integration\Base\DependencyInjection;

use Delos\Collection;
use Delos\Container;
use Delos\Controller\ControllerUtils;
use Delos\Instantiator;
use Delos\Parser\XmlParser;
use Delos\Request\GetVars;
use Delos\Request\Request;
use Delos\Request\Server;
use Delos\Routing\RouterXml;
use Delos\Security\Access;
use Delos\Shared\Directory;
use Delos\Shared\File;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Base\DependencyInjection\TestClasses\ClassFive;
use Tests\Integration\Base\DependencyInjection\TestClasses\ClassFour;
use Tests\Integration\Base\DependencyInjection\TestClasses\ClassOne;
use Tests\Integration\Base\DependencyInjection\TestClasses\ClassTwo;
use Twig\Environment;

class ContainerInstantiatorTest extends TestCase
{
    private Container $container;
    private Instantiator $instantiator;

    public function setUp(): void
    {
        $this->instantiator = new Instantiator(
            File::createFromString(realpath(".") . "/framework/routing.xml"),
            Directory::createFromString(realpath("."))
        );
        $this->container = new Container(
            new Collection(),
            $this->instantiator
        );
    }

    public function testGetRequest(): void
    {
        $request = $this->container->getRequest();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testGetRouterXml(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $get = $this->createMock(GetVars::class);
        $server = $this->createMock(Server::class);
        $request->get = $get;
        $request->server = $server;
        $get->expects(self::once())->method("getRawData")->willReturn(
            [
                "url" => "/my-url",
                "parameter" => [
                    "name" => "cool"
                ],
                "language" => "en"
            ]
        );
        $this->container->setService(Request::class, $request);

        $router = $this->container->getRouter();
        $this->assertInstanceOf(RouterXml::class, $router);
    }

    public function testGetXmlParser(): void
    {
        $parser = $this->container->getXmlParser();
        $this->assertInstanceOf(XmlParser::class, $parser);
    }

    public function testGetAccessChecker(): void
    {
        $accessChecker = $this->container->getAccessChecker();
        $this->assertInstanceOf(Access::class, $accessChecker);
    }

    public function testGetControlUtils(): void
    {
        $controlUtils = $this->container->getControllerUtils();
        $this->assertInstanceOf(ControllerUtils::class, $controlUtils);
    }

    public function testGetTwigService(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $server = $this->createMock(Server::class);
        $get = $this->createMock(GetVars::class);
        $request->get = $get;
        $request->server = $server;
        $get->expects(self::once())->method("getRawData")->willReturn(
            [
                "url" => "/my-url",
                "parameter" => [
                    "name" => "cool"
                ],
                "language" => "en"
            ]
        );
        $this->container->setService(Request::class, $request);

        $twig = $this->container->getTwig();
        $this->assertInstanceOf(Environment::class, $twig);
    }

    public function testIsServiceSet(): void
    {
        $this->container->getControllerUtils();
        $this->assertTrue($this->container->isServiceSet(ControllerUtils::class));
    }

    public function testClassObjectInstantiation(): void
    {
        $this->instantiator->setNamespacesBase(
            ["Tests\Integration\Base\DependencyInjection\TestClasses"]
        );
        $instance = $this->container->getService(ClassOne::class);

        $this->assertInstanceOf(ClassOne::class, $instance);
    }

    public function testClassObjectInstantiationRecursionOne(): void
    {
        $this->instantiator->setNamespacesBase(
            ["Tests\Integration\Base\DependencyInjection\TestClasses"]
        );
        $instance = $this->container->getService(ClassTwo::class);

        $this->assertInstanceOf(ClassTwo::class, $instance);
    }

    public function testClassObjectInstantiationRecursionTwoAndInterface(): void
    {
        $this->instantiator->setNamespacesBase(
            ["Tests\Integration\Base\DependencyInjection\TestClasses"]
        );
        $instance = $this->container->getService(ClassFour::class);
        $this->assertInstanceOf(ClassFour::class, $instance);
    }

    public function testClassObjectInstantiationRecursionFiveAndInterface(): void
    {
        $this->instantiator->setNamespacesBase(
            ["Tests\Integration\Base\DependencyInjection\TestClasses"]
        );
        $instance = $this->container->getService(ClassFive::class);
        $this->assertInstanceOf(ClassFive::class, $instance);
    }
}