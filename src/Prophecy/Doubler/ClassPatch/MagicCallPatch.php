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
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;
use RuntimeException;

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
        $parentClass = $node->getParentClass();
        $reflectionClass = new \ReflectionClass($parentClass);

        $phpdoc = new DocBlock($reflectionClass->getDocComment());

        $tagList = $phpdoc->getTagsByName('method');

        foreach($tagList as $tag) {
            try {
                $method = $this->parseMethod($tag);

                if (!$reflectionClass->hasMethod($method->getName())) {
                    $node->addMethod($method);
                }
            } catch(RuntimeException $e) {
                // Whats happens we cannot parse method tag? should we issue a warning here?
            }
        }
    }

    /**
     * @param $tag
     * @return MethodNode
     */
    private function parseMethod(MethodTag $tag)
    {
        $methodNode = new MethodNode($tag->getMethodName());
        $methodNode->setStatic($tag->isStatic());

        foreach ($tag->getArguments() as $arg) {
            $methodNode->addArgument(
                $this->parseArgument(implode(' ', $arg))
            );
        }

        return $methodNode;
    }

    /**
     * @param string $argument
     * @return ArgumentNode
     */
    private function parseArgument($argument)
    {
        if (preg_match('/^([\w\\\\]+)?\s*\$?(\w+)(?:\s*=\s*[\'\"]?([\w\\\\:]+)[\'\"]?)?/', $argument, $matches)) {
            $argumentNode = new ArgumentNode($matches[2]);

            if (!empty($matches[1])) {
                $argumentNode->setTypeHint($matches[1]);
            }

            if (!empty($matches[3])) {
                $argumentNode->setDefault($matches[3]);
            }

            return $argumentNode;
        } else {
            throw new RuntimeException("Invalid argument format");
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