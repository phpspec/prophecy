<?php

namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;
use Prophecy\Doubler\Generator\Node\ArgumentNode;

/**
 * Traverses all arguments for all methods on a ClassNode
 * checks the name for "..." as most extensions add arguments with that
 * name to indicate a varying number.
 *
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class ReflectionPatch implements ClassPatchInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(ClassNode $node)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(ClassNode $node)
    {
        foreach ($node->getMethods() as $method) {
            foreach ($method->getArguments() as $i => $argument) {
                if ($argument->getName() == '...') {
                    $argument->setName('arg' . ($i + 1));
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return 150;
    }
}
