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
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

/**
 * Discover Magical API using @method PHPDoc format.
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
        $parentClass = $node->getParentClass();
        $reflectionClass = new \ReflectionClass($parentClass);

        $phpdoc = new DocBlock($reflectionClass->getDocComment());

        $tagList = $phpdoc->getTagsByName('method');

        foreach($tagList as $tag) {
            $methodName = $tag->getMethodName();
            if (!$reflectionClass->hasMethod($methodName)) {
                $methodNode = new MethodNode($methodName);
                $methodNode->setStatic($tag->isStatic());

                foreach ($tag->getArguments() as $argument) {
                    $methodNode->addArgument($this->parseArgument($argument));
                }

                $node->addMethod($methodNode);
            }
        }
    }

    /**
     * @param array $argument
     * @return ArgumentNode
     */
    protected function parseArgument(array $argument) {
        $eqPos = array_search('=', $argument);
        $optional = $eqPos !== false;
        $name = $optional ? $argument[$eqPos - 1] : end($argument);

        $argumentNode = new ArgumentNode(ltrim($name, '$'));

        if ($optional && isset($argument[$eqPos + 1])) {
            $argumentNode->setDefault(trim($argument[$eqPos + 1], "'\""));
        }

        return $argumentNode;
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

