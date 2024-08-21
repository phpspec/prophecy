<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\InvalidArgumentException;

/**
 * Method node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MethodNode
{
    private $name;
    private $code;
    /**
     * @var string
     *
     * @phpstan-var 'public'|'private'|'protected'
     */
    private $visibility = 'public';
    /**
     * @var bool
     */
    private $static = false;
    /**
     * @var bool
     */
    private $returnsReference = false;

    /** @var ReturnTypeNode */
    private $returnTypeNode;

    /**
     * @var list<ArgumentNode>
     */
    private $arguments = array();

    // Used to accept an optional third argument with the deprecated Prophecy\Doubler\Generator\TypeHintReference so careful when adding a new argument in a minor version.
    /**
     * @param string      $name
     * @param string|null $code
     */
    public function __construct($name, $code = null)
    {
        $this->name = $name;
        $this->code = $code;
        $this->returnTypeNode = new ReturnTypeNode();
    }

    /**
     * @return string
     *
     * @phpstan-return 'public'|'private'|'protected'
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     *
     * @return void
     */
    public function setVisibility($visibility)
    {
        $visibility = strtolower($visibility);

        if (!\in_array($visibility, array('public', 'private', 'protected'), true)) {
            throw new InvalidArgumentException(sprintf(
                '`%s` method visibility is not supported.', $visibility
            ));
        }

        $this->visibility = $visibility;
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * @param bool $static
     *
     * @return void
     */
    public function setStatic($static = true)
    {
        $this->static = (bool) $static;
    }

    /**
     * @return bool
     */
    public function returnsReference()
    {
        return $this->returnsReference;
    }

    /**
     * @return void
     */
    public function setReturnsReference()
    {
        $this->returnsReference = true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function addArgument(ArgumentNode $argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * @return list<ArgumentNode>
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @deprecated use getReturnTypeNode instead
     * @return bool
     */
    public function hasReturnType()
    {
        return (bool) $this->returnTypeNode->getNonNullTypes();
    }

    public function setReturnTypeNode(ReturnTypeNode $returnTypeNode): void
    {
        $this->returnTypeNode = $returnTypeNode;
    }

    /**
     * @deprecated use setReturnTypeNode instead
     * @param string $type
     *
     * @return void
     */
    public function setReturnType($type = null)
    {
        $this->returnTypeNode = ($type === '' || $type === null) ? new ReturnTypeNode() : new ReturnTypeNode($type);
    }

    /**
     * @deprecated use setReturnTypeNode instead
     * @param bool $bool
     *
     * @return void
     */
    public function setNullableReturnType($bool = true)
    {
        if ($bool) {
            $this->returnTypeNode = new ReturnTypeNode('null', ...$this->returnTypeNode->getTypes());
        } else {
            $this->returnTypeNode = new ReturnTypeNode(...$this->returnTypeNode->getNonNullTypes());
        }
    }

    /**
     * @deprecated use getReturnTypeNode instead
     * @return string|null
     */
    public function getReturnType()
    {
        if ($types = $this->returnTypeNode->getNonNullTypes()) {
            return $types[0];
        }

        return null;
    }

    public function getReturnTypeNode(): ReturnTypeNode
    {
        return $this->returnTypeNode;
    }

    /**
     * @deprecated use getReturnTypeNode instead
     * @return bool
     */
    public function hasNullableReturnType()
    {
        return $this->returnTypeNode->canUseNullShorthand();
    }

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        if ($this->returnsReference) {
            return "throw new \Prophecy\Exception\Doubler\ReturnByReferenceException('Returning by reference not supported', get_class(\$this), '{$this->name}');";
        }

        return (string) $this->code;
    }

    /**
     * @return void
     */
    public function useParentCode()
    {
        $this->code = sprintf(
            'return parent::%s(%s);', $this->getName(), implode(', ',
                array_map(array($this, 'generateArgument'), $this->arguments)
            )
        );
    }

    /**
     * @return string
     */
    private function generateArgument(ArgumentNode $arg)
    {
        $argument = '$'.$arg->getName();

        if ($arg->isVariadic()) {
            $argument = '...'.$argument;
        }

        return $argument;
    }
}
