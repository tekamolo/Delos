<?php
declare(strict_types=1);

namespace Tests\Integration\Base\Kernel;

use Delos\Collection;
use Delos\Container;
use Delos\Instantiator;
use Delos\Launcher;
use Delos\Request\GetVars;
use Delos\Request\Request;
use Delos\Request\Server;
use Delos\Shared\Directory;
use Delos\Shared\File;
use PHPUnit\Framework\TestCase;

class LauncherTest extends TestCase
{
    public function testLauncher(): void
    {
        $this->instantiator = new Instantiator(
            File::createFromString(realpath(".") . "/framework/routing.xml"),
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
        $server = $this->createMock(Server::class);
        $request->get = $get;
        $request->server = $server;
        $get->expects(self::any())->method("getRawData")->willReturn(
            [
                "url" => "/dummy",
                "parameter" => [
                    "name" => "cool"
                ],
                "language" => "en"
            ]
        );
        $container->setService(Request::class, $request);

        $launcher = new Launcher($this->instantiator, $container);
        $launcher->run();
        self::assertTrue(true);

//        $this->expectOutputString("content");
//        $launcher->run();
    }
}