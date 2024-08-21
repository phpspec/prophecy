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
 * Property node.
 */
class PropertyNode
{
    private $name;

    /**
     * @var string
     *
     * @phpstan-var 'public'|'private'|'protected'
     */
    private $visibility = 'public';

    /**
     * @var PropertyTypeNode
     */
    private $typeNode;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->typeNode = new PropertyTypeNode();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PropertyTypeNode
     */
    public function getTypeNode(): PropertyTypeNode
    {
        return $this->typeNode;
    }

    /**
     * @return void
     */
    public function setTypeNode(PropertyTypeNode $typeNode)
    {
        $this->typeNode = $typeNode;
    }

    /**
     * @return string
     *
     * @phpstan-return 'public'|'private'|'protected'
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     *
     * @return void
     *
     * @phpstan-param 'public'|'private'|'protected' $visibility
     */
    public function setVisibility(string $visibility)
    {
        $visibility = strtolower($visibility);

        if (!\in_array($visibility, array('public', 'private', 'protected'), true)) {
            throw new InvalidArgumentException(sprintf(
                '`%s` method visibility is not supported.', $visibility
            ));
        }

        $this->visibility = $visibility;
    }
}
