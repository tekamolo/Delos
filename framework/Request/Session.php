<?php
namespace Delos\Request;
/**
 * Handles session variables and actions.
 */
class Session extends ArrayVars
{
    /**
     * Overrides parent constructor.
     */
    public function __construct()
    {
        parent::__construct($_SESSION);
    }

    /**
     * Add desired variables to the session.
     *
     * @param string $name - Session variable (key) name.
     * @param mixed $value - Variable value.
     */
    public function setVar($name, $value)
    {
        if (!empty($name)) {
            $_SESSION[$name] = $value;
            $this->data[$name] = $value;
        }
    }

    /**
     * @param $name
     */
    public function purge($name)
    {
        unset($_SESSION[$name]);
        unset($this->data[$name]);
    }
}