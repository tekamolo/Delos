<?php
declare(strict_types=1);

namespace Delos\Parser;

use Delos\Shared\File;

final class XmlParser
{
    private File $file;
    private \SimpleXMLElement $parsedXml;

    public function __construct(File $file)
    {
        $this->file = $file;
    }


    private function getParsedXml(): \SimpleXMLElement
    {
        if (!empty($this->parsedXml)) {
            return $this->parsedXml;
        }

        $this->parsedXml = simplexml_load_file($this->file->getPath());
        return $this->parsedXml;
    }

    public function searchNodeByChildrenTagValue(string $tagName, string $value): \SimpleXMLElement
    {
        foreach ($this->getParsedXml()->children() as $c) {
            $nodeChild = $c->xpath($tagName);
            if (!empty($nodeChild[0])) {
                if ((string)$c->xpath($tagName)[0] === $value) {
                    return $c;
                }
            }
        }
    }

    public function getXpath($xpath): array
    {
        return $this->getParsedXml()->xpath($xpath);
    }
}