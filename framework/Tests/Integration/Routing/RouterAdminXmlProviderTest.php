<?php

namespace Delos\Tests\Integration\Routing;

use Delos\Parser\XmlParser;
use Delos\Routing\RouterAdminXmlProvider;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class RouterAdminXmlProviderTest extends \PHPUnit\Framework\TestCase
{
    public $file;

    public function setUp()
    {
        $content ='<?xml version="1.0" encoding="UTF-8"?>
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


        vfsStreamWrapper::register();
        $root = vfsStream::newDirectory('directory');
        vfsStreamWrapper::setRoot($root);

        $file = vfsStream::newFile('routing.xml');
        $file->setContent($content);
        $root->addChild($file);

        $this->file = vfsStream::url('directory/routing.xml');
    }

    public function testRouterAdminXmlProviderGetRouteByRequestBaseUrl()
    {
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url,$params] = $httpRouteProviderXml->getRouteByRequest(array("/","24","update"),"/24/update");

        $this->assertEquals("/",$url);
        $this->assertEquals(array("24","update"),$params);
    }

    public function testRouterAdminXmlProviderGetRouteByRequestBaseUrlEmpty()
    {
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url,$params] = $httpRouteProviderXml->getRouteByRequest(array("","24","update"),"/24/update");

        $this->assertEquals("/",$url);
        $this->assertEquals(array("24","update"),$params);
    }

    public function testRouterAdminXmlProviderGetRouteByRequestBaseUrlEmptyAll()
    {
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url,$params] = $httpRouteProviderXml->getRouteByRequest(array("/"),"/24/update");

        $this->assertEquals("/",$url);
        $this->assertEquals(array("/"),$params);
    }

    public function testRouterAdminXmlProviderGetRouteByRequest()
    {
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url,$params] = $httpRouteProviderXml->getRouteByRequest(array("fr","connexion","24","update"),"/fr/connexion/24/update");

        $this->assertEquals("/fr/connexion/",$url);
        $this->assertEquals(array("24","update"),$params);
    }

    /**
     * @throws \Delos\Exception\Exception
     * @throws Exception
     */
    public function testRouterAdminXmlProvider()
    {
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url,$params,$language] = $httpRouteProviderXml->getRouteByRequest(array("fr","connexion","24","update"),"/fr/connexion/24/update");
        $this->expectException(\Delos\Exception\Exception::class);
        $this->expectExceptionMessage("The class StartingPagesController does not exist!");
        $controller = $httpRouteProviderXml->getControllerByUrl($url,$language);

        //$this->assertEquals("/directory/centralpay-credentials/",$route);
    }

    public function testGettingRouteByAlias()
    {
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        [$url,$params,$language] = $httpRouteProviderXml->getRouteByRequest(array("fr","connexion","24","update"),"/fr/connexion/24/update");
        $route = $httpRouteProviderXml->getRoute("login");

        $this->assertEquals("login",$route[0]->attributes()[0]->__toString());
    }
}