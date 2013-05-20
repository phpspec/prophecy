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
 * Property node.
 */
class PropertyNode
{
    private $name;
    private $visibility = 'public';
    private $static = false;

    public function __construct($name)
    {
        $this->name = $name;
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
                '`%s` property visibility is not supported.', $visibility
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
}
