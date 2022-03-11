<?php
declare(strict_types=1);

namespace Tests\Unit\Translator;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;
use Delos\Service\Translator\SourceXml;
use PHPUnit\Framework\TestCase;

class SourceXmlTest extends TestCase
{
    private SourceXml $sourceXml;
    private XmlParser $parser;

    public function setUp(): void
    {
        $this->sourceXml = new SourceXml();
    }

    /**
     * @skip
     */
    public function testNoResourceFoundBadFolder()
    {
        $this->markTestSkipped('must be refactored.');
        $this->expectException(Exception::class);

        $this->sourceXml->setProjectFolder("testing");
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS", "en");
    }

    public function testNoValidLanguage()
    {
        $this->markTestSkipped('must be refactored.');
        $this->expectException(Exception::class);

        //correct folder
        $this->sourceXml->setProjectFolder(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS","es");
    }

    public function testGetTranslationMethodReturn(){
        $this->markTestSkipped('must be refactored.');

        $this->sourceXml->setProjectFolder(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS","en");
    }
}