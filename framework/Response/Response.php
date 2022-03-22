<?php
declare(strict_types=1);

namespace Delos\Response;

class Response implements ResponseInterface
{
    public string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function process(): void
    {
        echo $this->content;
    }
}