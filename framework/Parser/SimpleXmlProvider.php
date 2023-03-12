<?php

declare(strict_types=1);

namespace Delos\Parser;

use Delos\Shared\File;

final class SimpleXmlProvider implements ProvidesSimpleXmlUrlNodes
{
    public function __construct(
        private readonly File $file
    ) {
    }
    public function getSimpleXmlNodes(): \SimpleXMLElement
    {
        return simplexml_load_file($this->file->getPath());
    }
}