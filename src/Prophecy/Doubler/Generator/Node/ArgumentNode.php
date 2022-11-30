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

/**
 * Argument node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ArgumentNode
{
    private $name;
    /**
     * @var mixed
     */
    private $default;
    /**
     * @var bool
     */
    private $optional    = false;

    /**
     * @var bool
     */
    private $byReference = false;

    /**
     * @var bool
     */
    private $isVariadic  = false;

    /** @var ArgumentTypeNode */
    private $typeNode;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->typeNode = new ArgumentTypeNode();
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
    public function setTypeNode(ArgumentTypeNode $typeNode)
    {
        $this->typeNode = $typeNode;
    }

    public function getTypeNode() : ArgumentTypeNode
    {
        return $this->typeNode;
    }

    /**
     * @return bool
     */
    public function hasDefault()
    {
        return $this->isOptional() && !$this->isVariadic();
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     *
     * @return void
     */
    public function setDefault($default = null)
    {
        $this->optional = true;
        $this->default  = $default;
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * @param bool $byReference
     *
     * @return void
     */
    public function setAsPassedByReference($byReference = true)
    {
        $this->byReference = $byReference;
    }

    /**
     * @return bool
     */
    public function isPassedByReference()
    {
        return $this->byReference;
    }

    /**
     * @param bool $isVariadic
     *
     * @return void
     */
    public function setAsVariadic($isVariadic = true)
    {
        $this->isVariadic = $isVariadic;
    }

    /**
     * @return bool
     */
    public function isVariadic()
    {
        return $this->isVariadic;
    }

    /**
     * @deprecated use getArgumentTypeNode instead
     * @return string|null
     */
    public function getTypeHint()
    {
        $type = $this->typeNode->getNonNullTypes() ? $this->typeNode->getNonNullTypes()[0] : null;

        return $type ? ltrim($type, '\\') : null;
    }

    /**
     * @deprecated use setArgumentTypeNode instead
     * @param string|null $typeHint
     *
     * @return void
     */
    public function setTypeHint($typeHint = null)
    {
        $this->typeNode = ($typeHint === null) ? new ArgumentTypeNode() : new ArgumentTypeNode($typeHint);
    }

    /**
     * @deprecated use getArgumentTypeNode instead
     * @return bool
     */
    public function isNullable()
    {
        return $this->typeNode->canUseNullShorthand();
    }

    /**
     * @deprecated use getArgumentTypeNode instead
     * @param bool $isNullable
     *
     * @return void
     */
    public function setAsNullable($isNullable = true)
    {
        $nonNullTypes = $this->typeNode->getNonNullTypes();
        $this->typeNode = $isNullable ? new ArgumentTypeNode('null', ...$nonNullTypes) : new ArgumentTypeNode(...$nonNullTypes);
    }
}
