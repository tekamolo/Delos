<?php
declare(strict_types=1);

namespace Tests\Integration\Routing;

use Delos\Exception\ExceptionToJson;
use Delos\Parser\ProvidesSimpleXmlUrlNodes;
use Delos\Parser\XmlParser;
use Delos\Request\GetVars;
use Delos\Request\Request;
use Delos\Request\Server;
use Delos\Routing\RouterAdminXmlProvider;
use Delos\Routing\RouterXml;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RoutingXmlTest extends TestCase
{
    private MockObject|GetVars $get;
    private MockObject|Request $request;
    private MockObject|Server $server;
    private MockObject|RouterAdminXmlProvider $providerXml;

    private ProvidesSimpleXmlUrlNodes|MockObject $nodesProvider;

    private string $file;

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
                    <route alias="comment-create">
                        <url lang="en">/comment/</url>
                        <url lang="es">/es/comentario/</url>
                        <url lang="fr">/fr/commentaire/</url>
                        <controller>CommentController:commentCreate</controller>
                        <access>USER</access>
                        <methods>POST</methods>
                    </route>
                    <route alias="comment-get">
                        <url lang="en">/comment/</url>
                        <url lang="es">/es/comentario/</url>
                        <url lang="fr">/fr/commentaire/</url>
                        <controller>PictureController:differentMethod</controller>
                        <access>USER</access>
                        <methods>GET</methods>
                    </route>
                </routes>';

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
        $this->nodesProvider = $this->getMockBuilder(ProvidesSimpleXmlUrlNodes::class)->getMock();
        $this->nodesProvider->expects(self::once())->method('getSimpleXmlNodes')->willReturn(
            simplexml_load_string($content)
        );
    }

    public function RoutingRequestProvider(): array
    {
        return [
            'empty url' => [
                'AllGetParams' => ['url' => ''],
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'getParams' => [],
                'expectedParams' => array(),
            ],
            'slash root url' => [
                'AllGetParams' => ['url' => '/'],
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'getParams' => [],
                'expectedParams' => array(),
            ],
            'empty url slash url' => [
                'AllGetParams' => ['url' => '/34'],
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'getParams' => [],
                'expectedParams' => array("34"),
            ],
            'empty spanish' => [
                'AllGetParams' => ['url' => '/es/34'],
                'alias' => 'login',
                'language' => 'es',
                'expectedUrl' => '/es/',
                'getParams' => [],
                'expectedParams' => array("34"),
            ],
            'url, no params' => [
                'AllGetParams' => ['url' => '/user-creation/'],
                'alias' => 'user-creation',
                'language' => 'en',
                'expectedUrl' => '/user-creation/',
                'getParams' => [],
                'expectedParams' => array(),
            ],
            'url, no params with no url at the end' => [
                'AllGetParams' => ['url' => '/user-creation'],
                'alias' => 'user-creation',
                'language' => 'en',
                'expectedUrl' => '/user-creation/',
                'getParams' => [],
                'expectedParams' => array(),
            ],
            'url not mapped, should return only params' => [
                'AllGetParams' => ['url' => '/sites/34/21-04-2017'],
                'alias' => 'login',
                'language' => 'en',
                'expectedUrl' => '/',
                'getParams' => [],
                'expectedParams' => array("sites","34","21-04-2017"),
            ],
            'url, params and get params' => [
                'AllGetParams' => [
                    'url' => '/es/navegador/34/21-04-2017',
                    'id' => '1',
                    'page' => '1',
                ],
                'alias' => 'browser',
                'language' => 'es',
                'expectedUrl' => '/es/navegador/',
                'getParams' => [
                    'id' => '1',
                    'page' => '1',
                ],
                'expectedParams' => array("34", "21-04-2017"),
            ],
        ];
    }

    /**
     * @dataProvider RoutingRequestProvider
     */
    public function testProcessUrlRequestMatching(
        array $AllGetParams,
        string $alias,
        string $language,
        string $expectedUrl,
        array $getParams,
        array  $expectedParams
    ): void
    {
        $this->get->expects(self::any())
            ->method('getRawData')
            ->willReturn(
                $AllGetParams
            );
        $this->request->get = $this->get;
        $parser = new XmlParser(
            $this->nodesProvider
        );


        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        $router = new RouterXml($this->request, $httpRouteProviderXml);
        $this->assertEquals($alias, $router->getCurrentAlias());
        $this->assertEquals($language, $router->getCurrentLanguage());
        $this->assertEquals($expectedUrl, $router->getCurrentUrl());
        $this->assertEquals($expectedParams, $router->getParams());
        $this->assertEquals($getParams, $router->getGetUrlParams());

        $this->assertStringContainsString($expectedUrl, $router->getCurrentUrlWithParams());
    }

    public function testRouterRequest(): void
    {
        $this->get->expects(self::any())
            ->method('getRawData')
            ->willReturn(
                array('url' => '/fr/utilisateur-creation/')
            );
        $this->request->get = $this->get;
        $parser = new XmlParser(
            $this->nodesProvider
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
        $this->get->expects(self::any())
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
            $this->nodesProvider
        );
        if($exception) {
            $this->expectException(ExceptionToJson::class);
        }
        $httpRouteProviderXml = new RouterAdminXmlProvider($parser);
        $router = new RouterXml($this->request, $httpRouteProviderXml);
        $this->assertEquals("USER", $router->getAccess());
    }
}