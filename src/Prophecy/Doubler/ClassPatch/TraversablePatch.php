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
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\Doubler\Generator\Node\Type\BuiltinType;

/**
 * Traversable interface patch.
 * Forces classes that implement interfaces, that extend Traversable to also implement Iterator.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class TraversablePatch implements ClassPatchInterface
{
    /**
     * Supports nodetree, that implement Traversable, but not Iterator or IteratorAggregate.
     *
     * @param ClassNode $node
     *
     * @return bool
     */
    public function supports(ClassNode $node)
    {
        if (in_array('Iterator', $node->getInterfaces())) {
            return false;
        }
        if (in_array('IteratorAggregate', $node->getInterfaces())) {
            return false;
        }

        foreach ($node->getInterfaces() as $interface) {
            if ('Traversable' !== $interface && !is_subclass_of($interface, 'Traversable')) {
                continue;
            }
            if ('Iterator' === $interface || is_subclass_of($interface, 'Iterator')) {
                continue;
            }
            if ('IteratorAggregate' === $interface || is_subclass_of($interface, 'IteratorAggregate')) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * Forces class to implement Iterator interface.
     *
     * @param ClassNode $node
     */
    public function apply(ClassNode $node)
    {
        $node->addInterface('Iterator');

        $currentMethod = new MethodNode('current');
        $currentMethod->setReturnTypeNode(new ReturnTypeNode(new BuiltinType('mixed')));
        $node->addMethod($currentMethod);

        $keyMethod = new MethodNode('key');
        $keyMethod->setReturnTypeNode(new ReturnTypeNode(new BuiltinType('mixed')));
        $node->addMethod($keyMethod);

        $nextMethod = new MethodNode('next');
        $nextMethod->setReturnTypeNode(new ReturnTypeNode(new BuiltinType('void')));
        $node->addMethod($nextMethod);

        $rewindMethod = new MethodNode('rewind');
        $rewindMethod->setReturnTypeNode(new ReturnTypeNode(new BuiltinType('void')));
        $node->addMethod($rewindMethod);

        $validMethod = new MethodNode('valid');
        $validMethod->setReturnTypeNode(new ReturnTypeNode(new BuiltinType('bool')));
        $node->addMethod($validMethod);
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority()
    {
        return 100;
    }
}
