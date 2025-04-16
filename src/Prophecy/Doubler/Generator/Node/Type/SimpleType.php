<?php

namespace Prophecy\Doubler\Generator\Node\Type;

class SimpleType extends AbstractType implements \Stringable
{
    private $type;
    public function __construct(string $type)
    {
        switch ($type) {
            // type aliases
            case 'double':
            case 'real':
                return 'float';
            case 'boolean':
                return 'bool';
            case 'integer':
                return 'int';

            //  built in types
            case 'self':
            case 'static':
            case 'array':
            case 'callable':
            case 'bool':
            case 'false':
            case 'true':
            case 'float':
            case 'int':
            case 'string':
            case 'iterable':
            case 'object':
            case 'null':
            case 'mixed':
            case 'void':
            case 'never':
                return $type;
            default:
                // Class / Interface type
                throw new \Exception('TODO');
                return $this->prefixWithNsSeparator($type);
        }
    }
}
