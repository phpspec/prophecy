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

final class BuiltinType implements SimpleType
{
    public function __construct(private readonly string $type) {}

    public function __toString(): string
    {
        return $this->getType();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function equals(TypeInterface $givenType): bool
    {
        if (!$givenType instanceof BuiltinType) {
            return false;
        }

        return $this->getType() === $givenType->getType();
    }
}
