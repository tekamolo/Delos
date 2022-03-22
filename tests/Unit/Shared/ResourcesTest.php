<?php
declare(strict_types=1);

namespace Tests\Unit\Shared;

use Delos\Exception\Exception;
use Delos\Shared\Directory;
use Delos\Shared\File;
use PHPUnit\Framework\TestCase;

class ResourcesTest extends TestCase
{
    public function testFileDoesNotExist(): void
    {
        $this->expectException(Exception::class);
        File::createFromString("randomStringPath");
    }

    public function testDirectoryDoesNotExist(): void
    {
        $this->expectException(Exception::class);
        Directory::createFromString("randomStringPath");
    }

    public function testDirectoryDoesExist(): void
    {
        $directory = Directory::createFromString("./");
        $this->assertSame("./", $directory->getPath());
        $this->assertInstanceOf(Directory::class, $directory);
    }

    public function testFileDoesExist(): void
    {
        $file = File::createFromString(__DIR__ . "/Dummy.php");
        $this->assertSame(__DIR__ . "/Dummy.php", $file->getPath());
        $this->assertInstanceOf(File::class, $file);

    }
}