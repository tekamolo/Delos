<?php
declare(strict_types=1);

namespace Tests\Integration\Routing;

use Delos\Exception\Exception;
use Delos\Parser\ProvidesSimpleXmlUrlNodes;
use Delos\Parser\XmlParser;
use Delos\Routing\RouterAdminXmlProvider;
use Delos\Shared\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

class RouterAdminXmlProviderTest extends TestCase
{
    public ProvidesSimpleXmlUrlNodes $nodesProvider;

    public function setUp(): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
                <routes namespaceBaseController="">
                    <route alias="login">
                        <url lang="en">/</url>
                        <url lang="es">/es/conexion/</url>
                        <url lang="fr">/fr/connexion/</url>
                        <controller>StartingPagesController:login</controller>
                        <access>USER</access>
                    </route>
                    <route alias="user-creation">
                        <url lang="en">/user-creation/</url>
                        <url lang="es">/es/usuario-creacion/</url>
                        <url lang="fr">/fr/utilisateur-creation/</url>
                        <controller>Main\MainController:userCreation</controller>
                        <access>USER</access>
                    </route>
                    <route alias="browser">
                        <url lang="en">/browser/</url>
                        <url lang="es">/es/navegador/</url>
                        <url lang="fr">/fr/navigateur/</url>
                        <controller>ObjectController:objectList</controller>
                        <access>USER</access>
                    </route>
                </routes>';

        $this->nodesProvider = $this->createMock(ProvidesSimpleXmlUrlNodes::class);
        $this->nodesProvider->expects(self::once())->method('getSimpleXmlNodes')->willReturn(
            simplexml_load_string($content)
        );
    }

    public function testRouterAdminXmlProviderGetRouteByRequestBaseUrl(): void
    {
        $parser = new XmlParser(
            $this->nodesProvider
        );

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url, $params] = $httpRouteProviderXml->getRouteByRequest(
            array("/", "random", "24"), "/random/","GET"
        );

        $this->assertEquals("/", $url);
        $this->assertEquals(array("random", "24"), $params);
    }

    public function testRouterAdminXmlProviderGetRouteByRequestBaseUrlEmpty(): void
    {
        $parser = new XmlParser(
            $this->nodesProvider
        );

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url, $params] = $httpRouteProviderXml->getRouteByRequest(
            array("", "24", "update"), "/24/update", "GET"
        );

        $this->assertEquals("/", $url);
        $this->assertEquals(array("24", "update"), $params);
    }

    public function testRouterAdminXmlProviderGetRouteByRequestBaseUrlEmptyAll(): void
    {
        $parser = new XmlParser(
            $this->nodesProvider
        );

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url, $params] = $httpRouteProviderXml->getRouteByRequest(
            array("/"), "/24/update", "GET"
        );

        $this->assertEquals("/", $url);
        $this->assertEquals(array("/"), $params);
    }

    public function testRouterAdminXmlProviderGetRouteByRequest(): void
    {
        $parser = new XmlParser(
            $this->nodesProvider
        );

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url, $params] = $httpRouteProviderXml->getRouteByRequest(
            array("fr", "connexion", "24", "update"), "/fr/connexion/24/update", "GET"
        );

        $this->assertEquals("/fr/connexion/", $url);
        $this->assertEquals(array("24", "update"), $params);
    }

    public function testRouterAdminXmlProvider(): void
    {
        $parser = new XmlParser(
            $this->nodesProvider
        );

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url, $params, $language] = $httpRouteProviderXml->getRouteByRequest(
            array("fr", "connexion", "24", "update"), "/fr/connexion/24/update", "GET"
        );
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The class StartingPagesController does not exist!");
        $controller = $httpRouteProviderXml->getSelectedNodeController($url, $language);

        //$this->assertEquals("/directory/centralpay-credentials/",$route);
    }

    public function testGettingRouteByAlias(): void
    {
        $parser = new XmlParser(
            $this->nodesProvider
        );

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url, $params, $language] = $httpRouteProviderXml->getRouteByRequest(
            array("fr", "connexion", "24", "update"), "/fr/connexion/24/update", "GET"
        );
        $route = $httpRouteProviderXml->getRoute("login");

        $this->assertEquals("login", $route[0]->attributes()[0]->__toString());
    }
}