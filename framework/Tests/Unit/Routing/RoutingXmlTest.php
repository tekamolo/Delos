<?php

use Delos\Request\GetVars;
use Delos\Request\Request;
use Delos\Routing\RouterAdminXmlProvider;
use Delos\Routing\RouterXml;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RoutingXmlTest extends TestCase
{
    public $get;

    /**
     * @var MockObject | Request
     */
    public $request;

    /**
     * @var MockObject|RouterAdminXmlProvider
     */
    public $providerXml;

    public function setUp()
    {
        $this->get = $this->getMockBuilder(GetVars::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->get = $this->get;

        $this->providerXml = $this->createMock(RouterAdminXmlProvider::class);
    }

    public function RoutingProvider()
    {
        return [
            'empty url slash url' => [
                'url' => '/',
                'expectedUrl' => '/',
                'expectedParams' => array(),
            ],
//            'url, no params' => [
//                'url' => '/centralpay-credentials/',
//                'expectedUrl' => '/centralpay-credentials/',
//                'expectedParams' => array(),
//            ],
//            'url, params' => [
//                'url' => '/sites/34/21-04-2017',
//                'expectedUrl' => '/sites/',
//                'expectedParams' => array("34","21-04-2017"),
//            ],
//            'url, params and get params' => [
//                'url' => '/sites/34/21-04-2017?id=1&page=1',
//                'expectedUrl' => '/sites/',
//                'expectedParams' => array("34","21-04-2017","id","1","page","1"),
//            ],
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
//        $this->get->expects($this->once())
//            ->method('getRawData')
//            ->willReturn(
//                array('url' => "/")
//            );
//        $this->request->get = $this->get;
//        /** @var RouterAdminXmlProvider|MockObject $providerXml */
//        $providerXml = $this->createMock(RouterAdminXmlProvider::class);
//        $providerXml
//            ->expects($this->once())
//            ->method("getRouteByRequest")
//            ->willReturn("testing");
//        $routerXml = new RouterXml($this->request,$providerXml);

//        $this->assertEquals($expectedUrl,$routerXml->getCurrentUrl());
//        $this->assertEquals($expectedParams,$routerXml->getParams());
//
//        if(!empty($expectedParams[0])){
//            $this->assertEquals($expectedParams[0],$routerXml->getParam(0));
//        }
    }

    public function testPregMatchSlashUrl(){
        preg_match_all("/([\w-]+)/", "/", $urlMatches);

        $this->assertEquals([[],[]],$urlMatches);
    }

    public function testPregMatchEmpyUrl(){
        preg_match_all("/([\w-]+)/", "", $urlMatches);

        $this->assertEquals([[],[]],$urlMatches);
    }

    public function testPregmatchFrenchUrl(){
        preg_match_all("/([\w-]+)/", "/fr/connexion/34", $urlMatches);

        $this->assertEquals(array("fr","connexion","34"),$urlMatches[0]);
    }
}