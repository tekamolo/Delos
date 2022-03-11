<?php
declare(strict_types=1);

namespace Delos\Service\Translator;

use Delos\Controller\ControllerUtils;
use Delos\Exception\Exception;

class Translator
{
    public const ENGLISH = "en";
    public const FRENCH = "fr";

    private const SOURCE_TYPE_XML = "xml";
    private const DEFAULT_LANGUAGE = self::ENGLISH;
    private SourceXml $source;
    private string $language = self::DEFAULT_LANGUAGE;
    private ControllerUtils $utils;

    private array $authorizedLanguages = array(
        self::ENGLISH,
        self::FRENCH
    );

    public function __construct(ControllerUtils $utils)
    {
        $this->utils = $utils;
        $this->setSource(self::SOURCE_TYPE_XML);
    }

    public function setSource(string $source = self::SOURCE_TYPE_XML): void
    {
        if ($source == self::SOURCE_TYPE_XML) {
            $this->source = new SourceXml();
            $this->source->setProjectFolder($this->utils->getProjectRoot());
        }
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getTranslation(string $index, string $language = null, array $placeholders = array()): string
    {
        $language = $this->getFilteredLanguage($language);
        $translation = $this->source->getTranslation($index, $language);
        $translation = mb_convert_encoding($translation, 'HTML-ENTITIES', "UTF-8");

        if (!empty($placeholders)) {
            foreach ($placeholders as $p => $replacement) {
                $translation = preg_replace('/{' . preg_quote($p) . '}/', (string)$replacement, $translation);
            }
        }

        return $translation;
    }

    public function trans(string $index, array $placeholders = array())
    {
        return $this->getTranslation($index, $this->language, $placeholders);
    }

    public function getFilteredLanguage(?string $language): string
    {
        if (empty($language)) {
            if (empty($this->language)) {
                throw new Exception("You have not set any language");
            }
            if (!in_array($this->language, $this->authorizedLanguages)) {
                throw new Exception("You are requesting an non existing language");
            }
            return $this->language;
        } else {
            if (!in_array($language, $this->authorizedLanguages)) {
                throw new Exception("You are requesting an non existing language");
            }
            return $language;
        }
    }
}