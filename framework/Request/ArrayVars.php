<?php
declare(strict_types=1);

namespace Delos\Request;

class ArrayVars extends VarFilter
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    protected function isVarSet(string $name): bool
    {
        return (!empty($this->data) && isset($this->data[$name]));
    }

    protected function getUnfiltered(string $name)
    {
        return $this->isVarSet($name) ? $this->data[$name] : null;
    }

    public function getKeys()
    {
        return array_keys($this->data);
    }
    public function dataAsJsonWithStringFilter(): string
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

    public function getRawData(): array
    {
        return $this->data;
    }
}
