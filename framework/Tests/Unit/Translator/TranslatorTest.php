<?php

namespace Delos\Tests\Unit\Translator;

use Delos\Service\Translator\SourceXml;
use Delos\Service\Translator\Translator;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /**
     * @var \Delos\Controller\ControllerUtils
     */
    private $controllerUtils;
    /**
     * @var \Delos\Service\Translator\Translator
     */
    private $translator;


    public function setUp()
    {
        $this->controllerUtils = $this->getMockBuilder(\Delos\Controller\ControllerUtils::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sourceXml = $this->getMockBuilder(SourceXml::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controllerUtils->expects($this->once())
            ->method('getProjectRoot')
            ->willReturn(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

        $sourceXml->expects($this->once())
            ->method("getTranslation")
            ->with("MY_INDEX", "en")
            ->willReturn("This is the batch {batchId}");

        $this->translator = new Translator($this->controllerUtils);
        $reflection = new \ReflectionClass($this->translator);
        $property = $reflection->getProperty('source');
        $property->setAccessible(true);
        $property->setValue($this->translator,$sourceXml);

    }

    public function testTranslation(){
        $result = $this->translator->getTranslation("MY_INDEX", "en",array("batchId" => "3lkf202r"));
        $this->assertEquals("This is the batch 3lkf202r",$result);
    }

    public function testShortHandFunction(){
        $result = $this->translator->trans("MY_INDEX",array("batchId" => "3lkf202r"));
        $this->assertEquals("This is the batch 3lkf202r",$result);
    }
}