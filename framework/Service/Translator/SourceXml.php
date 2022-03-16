<?php
declare(strict_types=1);

namespace Delos\Service\Translator;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;

class SourceXml
{
    protected string $file;
    protected string $projectFolder;
    protected array $xmlParsers = array();

    public function setProjectFolder(string $projectFolder): void
    {
        $this->projectFolder = $projectFolder;
    }


    private function getSourceFileName(string $language): string
    {
        if (Translator::ENGLISH == $language) {
            if (!file_exists($this->projectFolder . "/translations/en_EN.xml")) {
                throw new Exception("There was an error loading the xml source. directory: " . $this->projectFolder . "/translations/en_EN.xml");
            }
            $source = $this->projectFolder . "/translations/en_EN.xml";
        }
        if (Translator::FRENCH == $language) {
            if (!file_exists($this->projectFolder . "/translations/fr_FR.xml")) {
                throw new Exception("There was an error loading the xml source in " . __FILE__ . " line " . __LINE__);
            }
            $source = $this->projectFolder . "/translations/fr_FR.xml";
        }
        if (Translator::SPANISH == $language) {
            if (!file_exists($this->projectFolder . "/translations/es_ES.xml")) {
                throw new Exception("There was an error loading the xml source in " . __FILE__ . " line " . __LINE__);
            }
            $source = $this->projectFolder . "/translations/es_ES.xml";
        }
        if (empty($source)) {
            throw new Exception("No source was defined or could not be defined in " . __FILE__ . " line " . __LINE__);
        }
        return $source;
    }

    private function checkSourceFile(string $language): XmlParser
    {
        $source = $this->getSourceFileName($language);
        return $this->getXmlParser($source, $language);
    }

    private function getXmlParser(string $source, string $language): XmlParser
    {
        if (empty($this->xmlParsers[$language])) {
            $this->addXmlParsers(new XmlParser($source), $language);
        }
        return $this->xmlParsers[$language];
    }

    private function addXmlParsers(XmlParser $xmlParser, string $language): void
    {
        $this->xmlParsers[$language] = $xmlParser;
    }

    private function getNode(string $index, string $language): array
    {
        $parsedXml = $this->checkSourceFile($language);
        return $parsedXml->getXpath("/database/translation[@key='" . $index . "']");
    }

    public function getTranslation(string $index, string $language): string
    {
        $node = $this->getNode($index, $language);
        return $node[0]->__toString();
    }
}