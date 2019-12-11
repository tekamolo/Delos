<?php
namespace Delos\Request;
/**
 * Handles files variables and actions.
 */

class Files extends ArrayVars
{
    /**
     * Overrides parent constructor.
     */
    public function __construct()
    {
        parent::__construct( $_FILES );
    }

    /**
     * Add desired variables to the files.
     *
     * @param string $name - Files variable (key) name.
     * @param mixed $value - Variable value.
     */
    public function setVar( $name, $value )
    {
        if ( !empty( $name ) )
        {
            $_FILES[$name] = $value;
            $this->data[$name] = $value;
        }
    }
}