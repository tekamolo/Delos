<?php
declare(strict_types=1);

namespace Delos\Request;

class GetVars extends ArrayVars
{
    /**
     * Overrides parent constructor.
     */
    public function __construct()
    {
        parent::__construct($_GET);
    }
}