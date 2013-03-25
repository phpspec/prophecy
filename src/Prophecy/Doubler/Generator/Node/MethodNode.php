<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\InvalidArgumentException;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Method node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MethodNode
{
    private $name;
    private $visibility = 'public';
    private $static = false;
    private $arguments = array();

    public function __construct($name, $code = null)
    {
        $this->name = $name;
        $this->code = $code;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setVisibility($visibility)
    {
        $visibility = strtolower($visibility);

        if (!in_array($visibility, array('public', 'private', 'protected'))) {
            throw new InvalidArgumentException(sprintf(
                '`%s` method visibility is not supported.', $visibility
            ));
        }

        $this->visibility = $visibility;
    }

    public function isStatic()
    {
        return $this->static;
    }

    public function setStatic($static = true)
    {
        $this->static = (bool) $static;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addArgument(ArgumentNode $argument)
    {
        $this->arguments[] = $argument;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return (string) $this->code;
    }

    public function useParentCode()
    {
        $this->code = sprintf(
            'return parent::%s(%s);', $this->getName(), implode(', ',
                array_map(function($arg) { return '$'.$arg->getName(); }, $this->arguments)
            )
        );
    }
}
