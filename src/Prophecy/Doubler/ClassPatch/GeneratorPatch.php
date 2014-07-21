<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

/**
 * @author Florian Klein <florian.klein@free.fr>
 */
class GeneratorPatch implements ClassPatchInterface
{
    /**
     * @param ClassNode $node
     *
     * @return bool
     */
    public function supports(ClassNode $node)
    {
        return $node->getParentClass() === 'Generator';
    }

    /**
     * Forces class to implement Iterator interface.
     *
     * @param ClassNode $node
     */
    public function apply(ClassNode $node)
    {
        foreach (array('current', 'key', 'next', 'rewind', 'valid', 'send', 'throw') as $name) {
            $node->addMethod(new MethodNode($name));
        }
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority()
    {
        return 200;
    }
}
