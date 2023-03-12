<?php
declare(strict_types=1);

namespace Delos\Service\Translator;

use Delos\Exception\Exception;
use Delos\Parser\SimpleXmlProvider;
use Delos\Parser\XmlParser;
use Delos\Shared\Directory;
use Delos\Shared\File;

class SourceXml
{
    protected File $file;
    protected Directory $projectFolder;
    protected array $xmlParsers = array();

    public function setProjectFolder(Directory $projectFolder): void
    {
        $this->projectFolder = $projectFolder;
    }


    private function getSourceFileName(string $language): File
    {
        if (Translator::ENGLISH == $language) {
            if (!file_exists($this->projectFolder->getPath() . "/translations/en_EN.xml")) {
                throw new Exception("There was an error loading the xml source. directory: " . $this->projectFolder->getPath() . "/translations/en_EN.xml");
            }
            $source = $this->projectFolder->getPath() . "/translations/en_EN.xml";
        }
        if (Translator::FRENCH == $language) {
            if (!file_exists($this->projectFolder->getPath() . "/translations/fr_FR.xml")) {
                throw new Exception("There was an error loading the xml source in " . __FILE__ . " line " . __LINE__);
            }
            $source = $this->projectFolder->getPath() . "/translations/fr_FR.xml";
        }
        if (Translator::SPANISH == $language) {
            if (!file_exists($this->projectFolder->getPath() . "/translations/es_ES.xml")) {
                throw new Exception("There was an error loading the xml source in " . __FILE__ . " line " . __LINE__);
            }
            $source = $this->projectFolder->getPath() . "/translations/es_ES.xml";
        }
        if (empty($source)) {
            throw new Exception("No source was defined or could not be defined in " . __FILE__ . " line " . __LINE__);
        }
        return File::createFromString($source);
    }

    private function checkSourceFile(string $language): XmlParser
    {
        $source = $this->getSourceFileName($language);
        return $this->getXmlParser($source, $language);
    }

    private function getXmlParser(File $source, string $language): XmlParser
    {
        if (empty($this->xmlParsers[$language])) {
            $this->addXmlParsers(new XmlParser(
                new SimpleXmlProvider(
                    $source
                )
            ), $language);
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