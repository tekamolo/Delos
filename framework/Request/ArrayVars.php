<?php

namespace Delos\Request;
/**
 * Handles variables from array.
 */
class ArrayVars extends VarFilter
{

    /**
     * @var array to perform filtering on.
     */
    protected $data;

    /**
     * Class constructor.
     *
     * @param array $data Array to perform filtering on.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Checks if desired variable is set.
     *
     * @param string $name - Variable name.
     *
     * @return boolean - true - if variable is set, false - otherwise.
     */
    protected function isVarSet($name)
    {
        return (!empty($this->data) && isset($this->data[$name]));
    }

    /**
     * Returns desired unfiltered variable if it's set.
     *
     * @param string $name - Variable name.
     *
     * @return mixed - Variable value or null if it's not set.
     */
    protected function getUnfiltered($name)
    {
        return $this->isVarSet($name) ? $this->data[$name] : null;
    }

    /**
     * Get keys of data array.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->data);
    }

    /**
     * @return string
     */
    public function dataAsJsonWithStringFilter()
    {
        $result = array();
        foreach ($this->getKeys() as $key) {

            if (is_array($this->get($key))) {
                throw new InvalidArgumentException('Nested arrays are not supported');
            }

            $result[$key] = $this->get($key, self::STRING);
        }

        return json_encode($result);
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }
}
