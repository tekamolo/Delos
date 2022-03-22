<?php
declare(strict_types=1);

namespace Tests\Integration\Base\Kernel;

use Delos\Collection;
use Delos\Container;
use Delos\Instantiator;
use Delos\Launcher;
use Delos\Request\GetVars;
use Delos\Request\Request;
use Delos\Shared\Directory;
use Delos\Shared\File;
use PHPUnit\Framework\TestCase;

class LauncherTest extends TestCase
{
    public function testLauncher(): void
    {
        $instantiator = $this->instantiator = new Instantiator(
            File::createFromString(__DIR__ . "/routing.xml"),
            Directory::createFromString(realpath("."))
        );

        $container = new Container(
            new Collection(),
            $this->instantiator
        );
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $get = $this->createMock(GetVars::class);
        $request->get = $get;
        $get->expects(self::once())->method("getRawData")->willReturn(
            [
                "url" => "/my-url",
                "parameter" => [
                    "name" => "cool"
                ],
                "language" => "en"
            ]
        );
        $container->setService(Request::class, $request);

        $launcher = new Launcher($instantiator, $container);

        $this->expectOutputString("content");
        $launcher->run();
    }
}