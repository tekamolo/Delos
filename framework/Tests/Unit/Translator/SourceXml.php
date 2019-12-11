<?php

namespace Delos\Tests\Unit\Translator;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;
use Delos\Service\Translator\SourceXml as SourceXmlTrans;

class SourceXml extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SourceXmlTrans
     */
    private $sourceXml;

    private $parser;

    public function setUp()
    {
        $this->sourceXml = new SourceXmlTrans();
        $this->parser = $this->getMockBuilder(XmlParser::class)->getMock();

        $this->sourceXml->setXmlParser($this->parser);
    }

    public function testNoResourceFoundBadFolder(){
        $this->expectException(Exception::class);

        $this->sourceXml->setProjectFolder("testing");
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS","en");
    }

    public function testNoValidLanguage(){
        $this->expectException(Exception::class);

        //correct folder
        $this->sourceXml->setProjectFolder(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS","es");
    }

    public function testGetTranslationMethodReturn(){
        $this->parser->expects($this->once())
            ->method("getXpath")
            ->with("/database/translation[@key='BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS']")
            ->willReturn(
                array(
                    new \SimpleXMLElement("<translation key=\"BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS\">Your bank transfer(s) has (have) been validated</translation>")
                )
            );

        $this->sourceXml->setProjectFolder(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS","en");
    }
}