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
 * SplFileObject patch.
 * Makes SplFileObject and derivative classes usable with Prophecy.
 * By overriding default values which prevent SplFileObject being instantiated
 *
 * @author Artur Wielogorski <wodor@wodor.net>
 */
class SplFileObjectPatch implements ClassPatchInterface
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

        return 'SplFileObject' === $node->getParentClass()
        || is_subclass_of($node->getParentClass(), 'SplFileObject');
    }

    /**
     * Updated constructor code to call parent one with dummy file argument.
     *
     * @param ClassNode $node
     */
    public function apply(ClassNode $node)
    {
        if ($node->hasMethod('__construct')) {
            $constructor = $node->getMethod('__construct');
        } else {
            $constructor = new MethodNode('__construct');
            $node->addMethod($constructor);
        }

        // FIXME find a proper way to find out if the construcor is overriden
        // i.e compare list of arguments
        if (is_subclass_of($node->getParentClass(), 'SplFileObject')) {
            $constructor->useParentCode();
            return;
        }

        $constructor->setCode('return parent::__construct("' . __FILE__ . '");');
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority()
    {
        return 40;
    }
}
