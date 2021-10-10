<?php

namespace Delos\Tests\Integration\Translator;

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

        $this->controllerUtils->expects($this->any())
            ->method('getProjectRoot')
            ->willReturn(dirname(dirname(dirname(dirname(__DIR__)))));

        $this->translator = new Translator($this->controllerUtils);
    }

    public function testTranslationEnglish(){
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS","en");
        $this->assertEquals("Your bank transfer(s) has (have) been validated",$translation);
    }

    public function testTranslationFrench(){
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS","fr");
        $this->assertEquals("Votre (vos) transfert(s) a (ont) &eacute;t&eacute; valid&eacute;(s)",$translation);
    }

    public function testPlaceHolder(){
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_BODY_SUCCESS","en",array("batchId" => 145));
        $this->assertEquals("We have received the funds for your batch no. 145 and your bank transfer(s) will be dispatched.",$translation);
    }

    public function testLanguageSwitch(){
        $this->translator->setLanguage(Translator::FRENCH);
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS");
        $this->assertEquals("Votre (vos) transfert(s) a (ont) &eacute;t&eacute; valid&eacute;(s)",$translation);

        $this->translator->setLanguage(Translator::ENGLISH);
        $translation = $this->translator->getTranslation("BANK_TRANSFER_EMAIL_SUBJECT_SUCCESS");
        $this->assertEquals("Your bank transfer(s) has (have) been validated",$translation);
    }
}