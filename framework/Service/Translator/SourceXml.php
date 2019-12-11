<?php

namespace Delos\Service\Translator;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;

class SourceXml
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $projectFolder;

    /**
     * @var XmlParser
     */
    protected $xmlParsers = array();

    /**
     * @param string $projectFolder
     */
    public function setProjectFolder($projectFolder)
    {
        $this->projectFolder = $projectFolder;
    }

    /**
     * @param $language
     * @return string
     * @throws Exception
     */
    private function getSourceFileName($language){
        if (Translator::ENGLISH == $language) {
            if (!file_exists($this->projectFolder . "/translations/en_EN.xml")) {
                throw new Exception("There was an error loading the xml source");
            }
            $source = $this->projectFolder . "/translations/en_EN.xml";
        }
        if (Translator::FRENCH == $language) {
            if (!file_exists($this->projectFolder . "/translations/fr_FR.xml")) {
                throw new Exception("There was an error loading the xml source in " . __FILE__ . " line " . __LINE__);
            }
            $source = $this->projectFolder . "/translations/fr_FR.xml";
        }
        if (empty($source)) {
            throw new Exception("No source was defined or could not be defined in " . __FILE__ . " line " . __LINE__);
        }
        return $source;
    }

    /**
     * @param string $language
     * @return XmlParser
     * @throws Exception
     */
    private function checkSourceFile($language)
    {
        $source = $this->getSourceFileName($language);
        return $this->getXmlParser($source,$language);
    }

    /**
     * @param $source
     * @param $language
     * @return XmlParser
     */
    private function getXmlParser($source, $language){
        if(empty($this->xmlParsers[$language])){
            $this->addXmlParsers(new XmlParser($source),$language);
        }
        return $this->xmlParsers[$language];
    }

    /**
     * @param XmlParser $xmlParser
     * @param $language
     */
    private function addXmlParsers($xmlParser,$language)
    {
        $this->xmlParsers[$language] = $xmlParser;
    }

    /**
     * @param $index
     * @param $language
     * @return \SimpleXMLElement[]
     * @throws Exception
     */
    private function getNode($index, $language)
    {
        $parsedXml = $this->checkSourceFile($language);
        return $parsedXml->getXpath("/database/translation[@key='" . $index . "']");
    }

    /**
     * @param $index
     * @param $language
     * @return string
     * @throws Exception
     */
    public function getTranslation($index, $language)
    {
        $node = $this->getNode($index, $language);
        return $node[0]->__toString();
    }
}