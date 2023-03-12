<?php
declare(strict_types=1);

namespace Delos\Parser;

final class XmlParser
{
    private \SimpleXMLElement $parsedXml;

    public function __construct(
        private readonly ProvidesSimpleXmlUrlNodes $nodesProvider,
    ) {
    }


    private function getParsedXml(): \SimpleXMLElement
    {
        if (!empty($this->parsedXml)) {
            return $this->parsedXml;
        }

        $this->parsedXml = $this->nodesProvider->getSimpleXmlNodes();
        return $this->parsedXml;
    }

    public function searchNodeByChildrenTagValue(string $tagName, string $value): array
    {
        $matchingNodes = [];
        foreach ($this->getParsedXml()->children() as $c) {
            $nodeChild = $c->xpath($tagName);
            if (!empty($nodeChild[0])) {
                if ((string)$c->xpath($tagName)[0] === $value) {
                    $matchingNodes[] = $c;
                }
            }
        }
        return $matchingNodes;
    }

    public function getXpath($xpath): array
    {
        return $this->getParsedXml()->xpath($xpath);
    }
}