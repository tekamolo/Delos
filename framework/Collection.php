<?php
declare(strict_types=1);

namespace Delos;

class Collection
{

    private array $items = array();

    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add.
     *
     * @return Collection
     */
    public function add($element): Collection
    {
        $this->items[] = $element;

        return $this;
    }

    /**
     * Checks whether the collection is empty (contains no elements).
     *
     * @return boolean TRUE if the collection is empty, FALSE otherwise.
     */
    public function isEmpty(): bool
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
    public function remove($key): void
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
    public function containsKey($key): bool
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
    public function set($key, $value): Collection
    {
        $this->items[$key] = $value;

        return $this;
    }

    public function getAll(): array
    {
        return $this->items;
    }
}