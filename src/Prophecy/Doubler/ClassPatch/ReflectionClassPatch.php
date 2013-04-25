<?php

namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ReflectionClass patch.
 * Removes non-optional argument from newInstance() override.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ReflectionClassPatch implements ClassPatchInterface
{
    /**
     * Supports everything that extends SplFileInfo.
     *
     * @param ClassNode $node
     *
     * @return bool
     */
    public function supports(ClassNode $node)
    {
        if (null === $node->getParentClass()) {
            return false;
        }

        return 'ReflectionClass' === $node->getParentClass()
            || is_subclass_of($node->getParentClass(), 'ReflectionClass')
        ;
    }

    /**
     * Updated constructor code to call parent one with dummy file argument.
     *
     * @param ClassNode $node
     */
    public function apply(ClassNode $node)
    {
        $newInstance = $node->getMethod('newInstance');
        $newInstance->setArguments(array());
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority()
    {
        return 50;
    }
}
