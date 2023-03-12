<?php

declare(strict_types=1);

namespace Delos\Parser;

interface ProvidesSimpleXmlUrlNodes
{
    public function getSimpleXmlNodes(): \SimpleXMLElement;
}