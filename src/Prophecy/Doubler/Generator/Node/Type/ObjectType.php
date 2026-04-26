<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator\Node\Type;

final class ObjectType implements SimpleType
{
    /**
     * @param class-string $type
     */
    public function __construct(private readonly string $type) {}

    public function __toString(): string
    {
        return '\\'.$this->type;
    }

    /**
     * @return class-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function equals(TypeInterface $givenType): bool
    {
        if (!$givenType instanceof ObjectType) {
            return false;
        }

        return $this->getType() === $givenType->getType();
    }
}
