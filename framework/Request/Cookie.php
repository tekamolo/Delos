<?php
namespace Delos\Request;
/**
 * Handles cookie variables and actions.
 */

class Cookie extends ArrayVars
{
    /**
     * Default expiration time of a cookie in seconds.
     */
    const DEFAULT_EXPIRATION_TIME = 3600000;

    /**
     * Overrides parent constructor.
     */
    public function __construct()
    {
        parent::__construct( $_COOKIE );
    }

    /**
     * Add desired variables to cookies.
     *
     * @param string $name - Cookie variable (key) name.
     * @param mixed $value - Variable value.
     * @param int $time - The life time of the cookie in seconds.
     */
    public function setVar($name, $value, $time = self::DEFAULT_EXPIRATION_TIME, $location = null)
    {
        if (!empty($name)) {
            if (null === $location) {
                setcookie($name, $value, time() + $time, $location);
            } else {
                setcookie($name, $value, time() + $time);
            }
            $_COOKIE[$name] = $value;
            $this->data[$name] = $value;
        }
    }
}