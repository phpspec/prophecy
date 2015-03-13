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

use phpDocumentor\Reflection\DocBlock;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

/**
 * Discover Magical API using "@method" PHPDoc format.
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */
class MagicCallPatch implements ClassPatchInterface
{
    /**
     * Support any class
     *
     * @param ClassNode $node
     *
     * @return boolean
     */
    public function supports(ClassNode $node)
    {
        return true;
    }

    /**
     * Discover Magical API
     *
     * @param ClassNode $node
     */
    public function apply(ClassNode $node)
    {
        $this->attach($node, $node->getParentClass());

        $interfaces = $node->getInterfaces() ?: array();
        foreach ($interfaces as $interfaceName) {
            $this->attach($node, $interfaceName);
        }
    }

    /**
     * Discovers and attaches Magical API to node
     *
     * @param ClassNode $node
     * @param string $className
     */
    private function attach(ClassNode $node, $className)
    {
        $reflectionClass = new \ReflectionClass($className);
        $phpdoc = new DocBlock($reflectionClass->getDocComment());

        $tagList = $phpdoc->getTagsByName('method');

        foreach($tagList as $tag) {
            $methodName = $tag->getMethodName();

            if (!$node->hasMethod($methodName)) {
                $methodNode = new MethodNode($tag->getMethodName());
                $methodNode->setStatic($tag->isStatic());

                $node->addMethod($methodNode);
            }
        }
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return integer Priority number (higher - earlier)
     */
    public function getPriority()
    {
        return 50;
    }
}

