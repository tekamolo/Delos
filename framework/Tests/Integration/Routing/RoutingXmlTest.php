<?php
namespace Integration;

use Delos\Parser\XmlParser;
use Delos\Request\GetVars;
use Delos\Request\Request;
use Delos\Routing\RouterAdminXmlProvider;
use Delos\Routing\RouterXml;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class RoutingXmlTest extends \PHPUnit\Framework\TestCase
{
    public $get;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject | Request
     */
    public $request;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RouterAdminXmlProvider
     */
    public $providerXml;

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

        $this->get = $this->getMockBuilder(GetVars::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->get = $this->get;
    }

    public function RoutingProvider()
    {
        return [
            'empty url' => [
                'url' => '',
                'expectedUrl' => '/',
                'expectedParams' => array(),
            ],
            'empty url slash url' => [
                'url' => '/',
                'expectedUrl' => '/',
                'expectedParams' => array(),
            ],
            'url, no params' => [
                'url' => '/user-creation/',
                'expectedUrl' => '/user-creation/',
                'expectedParams' => array(),
            ],
            'url not mapped, should return only params' => [
                'url' => '/sites/34/21-04-2017',
                'expectedUrl' => '/',
                'expectedParams' => array("sites","34","21-04-2017"),
            ],
            'url, params and get params' => [
                'url' => '/es/navegador/34/21-04-2017?id=1&page=1',
                'expectedUrl' => '/es/navegador/',
                'expectedParams' => array("34","21-04-2017","id","1","page","1"),
            ],
        ];
    }

    /**
     * @dataProvider RoutingProvider
     * @param $url
     * @param $expectedUrl
     * @param $expectedParams
     */
    public function testProcessUrl($url,$expectedUrl,$expectedParams)
    {
        $this->get->expects($this->once())
            ->method('getRawData')
            ->willReturn(
                array('url' => $url)
            );
        $this->request->get = $this->get;
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        $router = new RouterXml($this->request,$httpRouteProviderXml);
        $this->assertEquals($expectedUrl,$router->getCurrentUrl());
        $this->assertEquals("USER",$router->getAccess());
        $this->assertEquals("/fr/utilisateur-creation/",$router->getUrl("user-creation","fr"));
        $this->assertEquals($expectedParams,$router->getParams());

        $this->assertStringContainsString($expectedUrl,$router->getCurrentUrlWithParams());
    }
}