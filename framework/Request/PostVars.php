<?php
namespace Delos\Request;
/**
 * Handles $_POST variables.
 */

class PostVars extends ArrayVars
{
    /**
     * Overrides parent constructor.
     */
    public function __construct()
    {
        parent::__construct($_POST);
    }
}