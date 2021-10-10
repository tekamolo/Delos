<?php
declare(strict_types=1);

namespace Delos\Parser;

class XmlParser
{
    private string $file;
    private \SimpleXMLElement $parsedXml;

    public function __construct($file)
    {
        $this->file = $file;
    }


    private function getParsedXml(): \SimpleXMLElement
    {
        if (!empty($this->parsedXml)) {
            return $this->parsedXml;
        }

        $this->parsedXml = simplexml_load_file($this->file);

        return $this->parsedXml;
    }

    public function searchNodeByChildrenTagValue(string $tagName, string $value): \SimpleXMLElement
    {
        foreach ($this->getParsedXml()->children() as $c) {
            $nodeChild = $c->xpath($tagName);
            if (!empty($nodeChild[0])) {
                if ((string)$c->xpath($tagName)[0] === $value) {
                    return $c;
                    break;
                }
            }
        }
    }

    public function getXpath($xpath): array
    {
        return $this->getParsedXml()->xpath($xpath);
    }
}