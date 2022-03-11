<?php
declare(strict_types=1);

namespace Tests\Extension;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

class BypassFinalHook implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}