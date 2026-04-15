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

use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Exception\InvalidArgumentException;

/**
 * Property node.
 */
final class PropertyNode
{
    private string $name;

    /** @phpstan-var 'public'|'private'|'protected' */
    private string $visibility;

    private TypeInterface $typeNode;

    /**
     * @phpstan-param 'public'|'private'|'protected' $visibility
     */
    public function __construct(string $name, string $visibility, TypeInterface $typeNode)
    {
        $this->name = $name;
        $this->setVisibility($visibility);
        $this->typeNode = $typeNode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return TypeInterface
     */
    public function getTypeNode(): TypeInterface
    {
        return $this->typeNode;
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
    public function setVisibility(string $visibility): void
    {
        if (!\in_array($visibility, array('public', 'private', 'protected'), true)) {
            throw new InvalidArgumentException(sprintf(
                '`%s` method visibility is not supported.', $visibility
            ));
        }

        $this->visibility = $visibility;
    }
}
