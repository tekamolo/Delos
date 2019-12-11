<?php

use Delos\Parser\XmlParser;
use Delos\Routing\RouterAdminXmlProvider;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class RouterAdminXmlProviderTest extends PHPUnit_Framework_TestCase
{
    public $file;

    public function setUp()
    {
        $content ='<?xml version="1.0" encoding="UTF-8"?>
                <routes>
                    <route alias="Alias">
                        <url>centralpay-credentials</url>
                        <controller>Admin\Sites:CentralPayCredentials</controller>
                        <access>MANAGEMENT|STAFF_SUPPORT</access>
                    </route>
                    <route alias="Alias-service">
                        <url>central-pay-credentials-service</url>
                        <controller>Admin\Sites:CentralPayCredentialsService</controller>
                        <access>MANAGEMENT|STAFF_SUPPORT</access>
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

    /**
     * @throws \Delos\Exception\Exception
     * @throws Exception
     */
    public function testRouterAdminXmlProvider()
    {
        $parser = new XmlParser($this->file);

        $httpRouteProviderXml = new RouterAdminXmlProvider($parser,"directory");
        $controller = $httpRouteProviderXml->getControllerByUrl("centralpay-credentials");
        $method = $httpRouteProviderXml->getMethodByUrl("centralpay-credentials");
        $access = $httpRouteProviderXml->getAccessByUrl("centralpay-credentials");

        $this->assertEquals("Delos\Controller\Admin\Sites",$controller);
        $this->assertEquals("CentralPayCredentialsAction",$method);
        $this->assertEquals("MANAGEMENT|STAFF_SUPPORT",$access);

        $route = $httpRouteProviderXml->getRoute("Alias");

        $this->assertEquals("/directory/centralpay-credentials/",$route);
    }
}