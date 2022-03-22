<?php
declare(strict_types=1);

namespace Tests\Integration\Base\Kernel;

use Delos\Response\Response;

class ControllerDummy
{
    public function pageOne(): Response
    {
        return new Response("content");
    }
}