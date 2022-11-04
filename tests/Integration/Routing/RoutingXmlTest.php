<?php
declare(strict_types=1);

namespace Tests\Integration\Routing;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;
use Delos\Request\GetVars;
use Delos\Request\Request;
use Delos\Request\Server;
use Delos\Routing\RouterAdminXmlProvider;
use Delos\Routing\RouterXml;
use Delos\Shared\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RoutingXmlTest extends TestCase
{
    public MockObject|GetVars $get;
    public MockObject|Request $request;
    public MockObject|Server $server;
    public MockObject|RouterAdminXmlProvider $providerXml;

    public string $file;

    public function setUp(): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
                <routes namespaceBaseController="">
                    <route alias="login">
                        <url lang="en">/</url>
                        <url lang="es">/es/</url>
                        <url lang="fr">/fr/</url>
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
                    <route alias="picture">
                        <url lang="en">/picture/</url>
                        <url lang="es">/es/foto/</url>
                        <url lang="fr">/fr/photo/</url>
                        <controller>PictureController:pictureList</controller>
                        <access>USER</access>
                        <methods>POST</methods>
                    </route>
                    <route alias="picture-all-methods">
                        <url lang="en">/picture-all-methods/</url>
                        <url lang="es">/es/picture-all-methods/</url>
                        <url lang="fr">/fr/picture-all-methods/</url>
                        <controller>PictureController:pictureList</controller>
                        <access>USER</access>
                    </route>
                    <route alias="picture-several-methods">
                        <url lang="en">/picture-several-methods/</url>
                        <url lang="es">/es/picture-several-methods/</url>
                        <url lang="fr">/fr/picture-several-methods/</url>
                        <controller>PictureController:pictureList</controller>
                        <access>USER</access>
                        <methods>POST|PUT</methods>
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
        $this->server = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->get = $this->get;
        $this->request->server = $this->server;
    }

    public function RoutingRequestProvider(): array
    {
        return [
            'empty url' => [
                'url' => '',
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'expectedParams' => array(),
            ],
            'slash root url' => [
                'url' => '/',
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'expectedParams' => array(),
            ],
            'empty url slash url' => [
                'url' => '/34',
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'expectedParams' => array("34"),
            ],
            'empty spanish' => [
                'url' => '/es/34',
                'alias' => 'login',
                'language' => 'es',
                'expectedUrl' => '/es/',
                'expectedParams' => array("34"),
            ],
            'url, no params' => [
                'url' => '/user-creation/',
                'alias' => 'user-creation',
                'language' => 'en',
                'expectedUrl' => '/user-creation/',
                'expectedParams' => array(),
            ],
            'url not mapped, should return only params' => [
                'url' => '/sites/34/21-04-2017',
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'expectedParams' => array("sites","34","21-04-2017"),
            ],
            'url, params and get params' => [
                'url' => '/es/navegador/34/21-04-2017?id=1&page=1',
                'alias' => 'browser',
                'language' => 'es',
                'expectedUrl' => '/es/navegador/',
                'expectedParams' => array("34", "21-04-2017", "id", "1", "page", "1"),
            ],
        ];
    }

    /**
     * @dataProvider RoutingRequestProvider
     */
    public function testProcessUrlRequestMatching(
        string $url,
        string $alias,
        string $language,
        string $expectedUrl,
        array  $expectedParams
    ): void
    {
        $this->get->expects($this->once())
            ->method('getRawData')
            ->willReturn(
                array('url' => $url)
            );
        $this->request->get = $this->get;
        $parser = new XmlParser(
            File::createFromString($this->file)
        );


        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        $router = new RouterXml($this->request, $httpRouteProviderXml);
        $this->assertEquals($alias, $router->getCurrentAlias());
        $this->assertEquals($language, $router->getCurrentLanguage());
        $this->assertEquals($expectedUrl, $router->getCurrentUrl());
        $this->assertEquals($expectedParams, $router->getParams());

        $this->assertStringContainsString($expectedUrl, $router->getCurrentUrlWithParams());
    }

    public function testRouterRequest(): void
    {
        $this->get->expects($this->once())
            ->method('getRawData')
            ->willReturn(
                array('url' => '/fr/utilisateur-creation/')
            );
        $this->request->get = $this->get;
        $parser = new XmlParser(
            File::createFromString($this->file)
        );
        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        $router = new RouterXml($this->request, $httpRouteProviderXml);
        $this->assertEquals("USER", $router->getAccess());
        $this->assertEquals("/fr/utilisateur-creation/", $router->getUrl("user-creation", "fr"));
    }

    public function requestProviderMethodsAllowed(): array
    {
        return [
            'method POST and Allowed Method matches' => [
                'url' => '/picture/',
                'method' => 'POST',
                'exception' => false,
            ],
            'method POST but no restrictions' => [
                'url' => '/picture-all-methods/',
                'method' => 'POST',
                'exception' => false,
            ],
            'method POST and restricted to POST AND PUT' => [
                'url' => '/picture-several-methods/',
                'method' => 'PUT',
                'exception' => false,
            ],
            'method GET and restricted to POST AND PUT' => [
                'url' => '/picture-several-methods/',
                'method' => 'GET',
                'exception' => true,
            ],
        ];
    }

    /**
     * @dataProvider requestProviderMethodsAllowed
     */
    public function testMethodAllowed(string $url, string $method, bool $exception): void
    {
        $this->get->expects($this->once())
            ->method('getRawData')
            ->willReturn(
                array('url' => $url)
            );
        $this->server
            ->method('getRequestMethod')
            ->with()
            ->willReturn($method);
        $this->request->get = $this->get;
        $parser = new XmlParser(
            File::createFromString($this->file)
        );
        if($exception) {
            $this->expectException(Exception::class);
        }
        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        $router = new RouterXml($this->request, $httpRouteProviderXml);
        $this->assertEquals("USER", $router->getAccess());
    }
}