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

use phpDocumentor\Reflection\DocBlock\Tags\Method;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Types\ContextFactory;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

/**
 * Discover Magical API using "@method" PHPDoc format.
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MagicCallPatch implements ClassPatchInterface
{
    private $docBlockFactory;
    private $contextFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
        $this->contextFactory = new ContextFactory();
    }

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

        try {
            $phpdoc = $this->docBlockFactory->create($reflectionClass, $this->contextFactory->createFromReflector($reflectionClass));
        } catch (\InvalidArgumentException $e) {
            // No DocBlock
        }

        /**
         * @var Method[] $tagList
         */
        $tagList = isset($phpdoc) ? $phpdoc->getTagsByName('method') : array();

        $interfaces = $reflectionClass->getInterfaces();
        foreach($interfaces as $interface) {
            try {
                $phpdoc = $this->docBlockFactory->create($interface, $this->contextFactory->createFromReflector($interface));
                $tagList = array_merge($tagList, $phpdoc->getTagsByName('method'));
            } catch (\InvalidArgumentException $e) {
                // No DocBlock
            }
        }

        foreach($tagList as $tag) {
            $methodName = $tag->getMethodName();

            if (!$reflectionClass->hasMethod($methodName)) {
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

