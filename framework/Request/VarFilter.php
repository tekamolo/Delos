<?php

namespace Delos\Request;

abstract class VarFilter
{
    /**
     * Filters.
     */
    const NONE = 'none';
    const STRING = 'string';
    //STRING_WITHOUT_ENTITIES differs from old STRING option. Old STRING option is using htmlentities which changes results
    const STRING_WITHOUT_ENTITIES = 'stringWithoutEntities';
    const INT = 'int';
    const FLOAT = 'float';

    /**
     * Checks if desired variable is set.
     *
     * @param string $name - Variable name.
     * @return boolean - true - if variable is set, false - otherwise.
     */
    abstract protected function isVarSet(string $name): bool;

    /**
     * Returns desired unfiltered variable if it's set.
     *
     * @param string $name - Variable name.
     * @return mixed - Variable value or null if it's not set.
     */
    abstract protected function getUnfiltered(string $name);

    /**
     * Returns a filtered variable or default value in case it is provided and desired variable is invalid or not set.
     *
     * @param string $name - Post variable name.
     * @param string $filter - Filter name. Default: NONE.
     * @param mixed $default - Default value in case of incorrect value. Default: null.
     * @return mixed - Filtered variable or default value.
     */
    public function get(string $name, string $filter = self::NONE, $default = null)
    {
        $result = $default;

        if ($this->isVarSet($name)) {
            $var = $this->getUnfiltered($name);

            switch ($filter) {
                case self::NONE:
                    $result = $var;
                    break;

                case self::STRING:
                    $var = stripslashes($var);
                    $var = htmlentities(trim($var), ENT_COMPAT, 'UTF-8');
                    $var = filter_var($var, FILTER_SANITIZE_STRING);
                    $result = $var;
                    break;

                case self::STRING_WITHOUT_ENTITIES:
                    $result = filter_var($var, FILTER_SANITIZE_STRING);
                    break;

                case self::INT:
                    $result = intval( $var );
                    break;

                case self::FLOAT:
                    $result = floatval( $var );
                    break;
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return int|mixed
     */
    public function getInt(string $name, $default = null)
    {
        return $this->get($name, self::INT, $default);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return string|mixed
     */
    public function getString(string $name, $default = null)
    {
        return $this->get($name, self::STRING, $default);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return string|mixed
     */
    public function getFloat(string $name, $default = null)
    {
        return $this->get($name, self::FLOAT, $default);
    }
}