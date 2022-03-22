<?php
declare(strict_types=1);

namespace Tests\Unit\Translator;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;
use Delos\Service\Translator\SourceXml;
use Delos\Shared\Directory;
use PHPUnit\Framework\TestCase;

class SourceXmlTest extends TestCase
{
    private SourceXml $sourceXml;
    private XmlParser $parser;

    public function setUp(): void
    {
        $this->sourceXml = new SourceXml();
        $this->sourceXml->setProjectFolder(
            Directory::createFromString(__DIR__)
        );
    }

    public function testNoResourceFoundBadFolder()
    {
        $this->expectException(Exception::class);

        $this->sourceXml->setProjectFolder(
            Directory::createFromString("random")
        );
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS", "en");
    }

    public function testNoValidLanguage()
    {
        $this->expectException(Exception::class);

        //correct folder
        $this->sourceXml->setProjectFolder(
            Directory::createFromString(realpath("."))
        );
        $this->sourceXml->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS", "es");
    }

    public function testGetTranslationMethodReturn()
    {
        $english = $this->sourceXml->getTranslation("welcome_delos", "en");
        $this->assertSame("Welcome to Delos!", $english);

        $spanish = $this->sourceXml->getTranslation("welcome_delos", "es");
        $this->assertSame("Bienvenido a Delos!", $spanish);
    }
}