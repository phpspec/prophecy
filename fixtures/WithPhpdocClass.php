<?php

namespace Fixtures\Prophecy;

/**
 * @method string name(string $gender = null)
 * @method mixed randomElement(array $array = array('a', 'b', 'c'))
 * @method mixed __unserialize($data)
 */
class WithPhpdocClass
{
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }
}
