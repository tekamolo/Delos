<?php

namespace Delos\Parser;

class XmlParser
{

    /**
     * @var string
     */
    private $file;

    /**
     * @var \SimpleXMLElement
     */
    private $parsedXml;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @return \SimpleXMLElement
     */
    private function getParsedXml()
    {
        if(!empty($this->parsedXml)){
            return $this->parsedXml;
        }

        $this->parsedXml = simplexml_load_file($this->file);

        return $this->parsedXml;
    }

    /**
     * @param string $tagName
     * @param string $value
     * @return \SimpleXMLElement
     */
    public function searchNodeByChildrenTagValue($tagName, $value)
    {
        foreach ($this->getParsedXml()->children() as $c){
            if((string) $c->xpath($tagName)[0] === $value){
                return $c;
                break;
            }
        }
    }

    /**
     * @param $xpath
     * @return \SimpleXMLElement[]
     */
    public function getXpath($xpath)
    {
        return $this->getParsedXml()->xpath($xpath);
    }
}