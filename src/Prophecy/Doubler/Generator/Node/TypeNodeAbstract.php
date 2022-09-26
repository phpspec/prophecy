<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\DoubleException;

abstract class TypeNodeAbstract
{
    /** @var string[] */
    protected $types = [];

    public function __construct(string ...$types)
    {
        $this->types = $types;
        $this->guardIsValidType();
    }

    public function canUseNullShorthand(): bool
    {
        return in_array('null', $this->types) && count($this->types) <= 2;
    }

    public function getTypes(): array
    {
        return array_values($this->types);
    }

    public function getNonNullTypes(): array
    {
        $nonNullTypes = $this->types;

        if (($key = array_search('null', $nonNullTypes)) !== false) {
            unset($nonNullTypes[$key]);
        }

        return array_values($nonNullTypes);
    }

    /**
     * Order of array does not matter. $array has to be non empty.
     *
     * @param $array
     * @return bool
     */
    protected function doesArrayEqual($array)
   {
       if (empty($this->types)) {
           return false;
       }
       $intersection = array_intersect($this->types, $array);

        return count($intersection) == count($this->types);
    }

    protected function guardIsValidType()
    {
        if(!empty($this->types) && count(array_intersect($this->types, ['false', 'null'])) == count($this->types)){
            throw new DoubleException('Type cannot be nullable false');
        }

        if ($this->doesArrayEqual(['null'])) {
            throw new DoubleException('Type cannot be standalone null');
        }

        if ($this->doesArrayEqual(['false'])) {
            throw new DoubleException('Type cannot be standalone false');
        }

        if ($this->doesArrayEqual(['false', 'null'])) {
            throw new DoubleException('Type cannot be nullable false');
        }

        if (\PHP_VERSION_ID >= 80000 && in_array('mixed', $this->types) && count($this->types) !== 1) {
            throw new DoubleException('mixed cannot be part of a union');
        }
    }
}
