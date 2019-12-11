<?php
namespace Delos\Request;
/**
 * Handles $_GET variables.
 */

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