<?php
declare(strict_types=1);

namespace Delos\Shared;

use Delos\Exception\Exception;
use JetBrains\PhpStorm\Pure;

abstract class Resource
{
    private string $path;

    private function __construct(string $file)
    {
        $this->path = $file;
    }

    public static function createFromString(string $file): static
    {
        if (false === file_exists($file)) {
            throw new Exception("the resource: " . $file . " does not exist");
        }
        return new static($file);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    #[Pure]
    public function updatePath(string $path): static
    {
        return new static($path);
    }
}