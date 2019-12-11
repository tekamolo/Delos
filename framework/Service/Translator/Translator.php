<?php

namespace Delos\Service\Translator;

use Delos\Controller\ControllerUtils;
use Delos\Exception\Exception;

class Translator
{
    const ENGLISH = "en";
    const FRENCH = "fr";

    const SOURCE_TYPE_XML = "xml";

    const DEFAULT_LANGUAGE = self::ENGLISH;

    /**
     * @var SourceXml
     */
    private $source;

    /**
     * @var string
     */
    private $language = self::DEFAULT_LANGUAGE;

    /**
     * @var ControllerUtils
     */
    private $utils;

    /**
     * @var array
     */
    private $authorizedLanguages = array(
        self::ENGLISH,
        self::FRENCH
    );

    /**
     * @param ControllerUtils $utils
     */
    public function __construct(ControllerUtils $utils)
    {
        $this->utils = $utils;
        $this->setSource(self::SOURCE_TYPE_XML);
    }

    /**
     * @param string $source
     */
    public function setSource($source = self::SOURCE_TYPE_XML)
    {
        if ($source == self::SOURCE_TYPE_XML) {
            $this->source = new SourceXml();
            $this->source->setProjectFolder($this->utils->getProjectRoot());
        }
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param $index
     * @param $language
     * @param array $placeholders
     * @return string
     * @throws Exception
     */
    public function getTranslation($index, $language = null, array $placeholders = array())
    {
        $language = $this->getFilteredLanguage($language);
        $translation = $this->source->getTranslation($index, $language);
        $translation = mb_convert_encoding($translation, 'HTML-ENTITIES', "UTF-8");

        if (!empty($placeholders)) {
            foreach ($placeholders as $p => $replacement) {
                $translation = preg_replace('/\{' . preg_quote($p) . '\}/', $replacement, $translation);
            }
        }

        return $translation;
    }

    /**
     * @param $index
     * @param array $placeholders
     * @return string
     * @throws Exception
     */
    public function trans($index, array $placeholders = array())
    {
        return $this->getTranslation($index, $this->language, $placeholders);
    }

    /**
     * @param string $language
     * @return string
     * @throws Exception
     */
    public function getFilteredLanguage($language)
    {
        if(empty($language)){
            if(empty($this->language)){
                throw new Exception("You have not set any language");
            }
            if (!in_array($this->language, $this->authorizedLanguages)) {
                throw new Exception("You are requesting an non existing language");
            }
            return $this->language;
        }else{
            if (!in_array($language, $this->authorizedLanguages)) {
                throw new Exception("You are requesting an non existing language");
            }
            return $language;
        }
    }
}