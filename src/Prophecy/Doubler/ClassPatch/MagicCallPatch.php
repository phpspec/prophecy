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
            $method = $this->parseMethod($tag);

            if (!$reflectionClass->hasMethod($method->getName())) {
                $node->addMethod($method);
            }
        }
    }

    /**
     * @param MethodTag $tag
     * @return MethodNode
     */
    private function parseMethod(MethodTag $tag)
    {
        $methodNode = new MethodNode($tag->getMethodName());
        $methodNode->setStatic($tag->isStatic());

        foreach ($tag->getArguments() as $arg) {
            try {
                $argument = $this->parseArgument(implode(' ', $arg));
                $methodNode->addArgument($argument);
            } catch (RuntimeException $e) {
                // just ignore incorrectly defined arguments
            }
        }

        return $methodNode;
    }

    /**
     * @param string $argument
     * @return ArgumentNode
     */
    private function parseArgument($argument)
    {
        /** @see http://php.net/manual/en/language.oop5.basic.php */
        $variable = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
        $class = '(?:' . $variable . '|\\\\)+';
        $default = '(?:\\\'.*\\\'|\".*\"|[.0-9]+[-+e.0-9]*|' . $class . '::' . $variable . '|' . $variable . ')';
        $pattern = '/^(' . $class . ')?\s*\$(' . $variable . ')(?:\s*=\s*(' . $default . '))?/';

        if (!preg_match($pattern, $argument, $matches)) {
            throw new RuntimeException("Invalid argument format");
        }

        $argumentNode = new ArgumentNode($matches[2]);

        if (!empty($matches[1])) {
            $argumentNode->setTypeHint($matches[1]);
        }

        if (!empty($matches[3])) {
            $argumentNode->setDefault(trim($matches[3], '\'"'));
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
