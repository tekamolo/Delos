<?php

namespace Delos;

class Collection
{

    private $items = array();

    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add.
     *
     * @return Collection
     */
    public function add($element)
    {
        $this->items[] = $element;

        return $this;
    }

    /**
     * Checks whether the collection is empty (contains no elements).
     *
     * @return boolean TRUE if the collection is empty, FALSE otherwise.
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|integer $key The kex/index of the element to remove.
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element.
     */
    public function remove($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|integer $key The key/index to check for.
     *
     * @return boolean TRUE if the collection contains an element with the specified key/index,
     *                 FALSE otherwise.
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to retrieve.
     *
     * @return mixed
     */
    public function get($key)
    {
        if ($this->containsKey($key))
            return $this->items[$key];
    }

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to set.
     * @param mixed $value The element to set.
     *
     * @return Collection
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;

        return $this;
    }

    /**
     * get all the elements of the collection
     *
     * @return array
     */
    public function getAll()
    {
        return $this->items;
    }
}