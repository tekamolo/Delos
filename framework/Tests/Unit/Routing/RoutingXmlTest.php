<?php

class RoutingXmlTest extends \PHPUnit\Framework\TestCase
{
    public $get;

    public $request;

    public $provider;

    public $providerXml;

    public function setUp()
    {
        $this->get = $this->getMockBuilder(GetVars::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder(Http_Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->get = $this->get;

        $this->provider = $this->createMock(Http_Routing_RouteProvider::class);
        $this->providerXml = $this->createMock(\Delos\Routing\RouterAdminXmlProvider::class);
    }

    public function RoutingProvider()
    {
        return [
            'url, no params' => [
                'url' => '/centralpay-credentials/',
                'expectedUrl' => 'centralpay-credentials',
                'expectedParams' => array(),
            ],
            'url, params' => [
                'url' => '/sites/34/21-04-2017',
                'expectedUrl' => 'sites',
                'expectedParams' => array("34","21-04-2017"),
            ],
            'url, params and get params' => [
                'url' => '/sites/34/21-04-2017?id=1&page=1',
                'expectedUrl' => 'sites',
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
        $routerXml = new \Delos\Routing\RouterXml($this->request,$this->provider,$this->providerXml);

        $this->assertEquals($expectedUrl,$routerXml->getCurrentUrl());
        $this->assertEquals($expectedParams,$routerXml->getParams());

        if(!empty($expectedParams[0])){
            $this->assertEquals($expectedParams[0],$routerXml->getParam(0));
        }
    }
}