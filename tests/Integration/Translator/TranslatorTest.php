<?php
declare(strict_types=1);

namespace Tests\Integration\Translator;

use Delos\Controller\ControllerUtils;
use Delos\Service\Translator\Translator;
use Delos\Shared\Directory;
use PHPUnit\Framework\TestCase;

final class TranslatorTest extends TestCase
{
    private ControllerUtils $controllerUtils;
    private Translator $translator;

    public function setUp(): void
    {
        $this->controllerUtils = $this->getMockBuilder(ControllerUtils::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controllerUtils->expects($this->any())
            ->method('getProjectRoot')
            ->willReturn(
                Directory::createFromString(realpath("."))
            );

        $this->translator = new Translator($this->controllerUtils);
    }

    public function testTranslationEnglish(): void
    {
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS", "en");
        $this->assertEquals("Your bank transfer(s) has (have) been validated", $translation);
    }

    public function testTranslationFrench(): void
    {
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS", "fr");
        $this->assertEquals("Votre (vos) transfert(s) a (ont) &eacute;t&eacute; valid&eacute;(s)", $translation);
    }

    public function testPlaceHolder(): void
    {
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_BODY_SUCCESS", "en", array("batchId" => 145));
        $this->assertEquals("We have received the funds for your batch no. 145 and your bank transfer(s) will be dispatched.", $translation);
    }

    public function testLanguageSwitch(): void
    {
        $this->translator->setLanguage(Translator::FRENCH);
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS");
        $this->assertEquals("Votre (vos) transfert(s) a (ont) &eacute;t&eacute; valid&eacute;(s)", $translation);

        $this->translator->setLanguage(Translator::ENGLISH);
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS");
        $this->assertEquals("Your bank transfer(s) has (have) been validated", $translation);
    }
}