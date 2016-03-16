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
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tag as LegacyTag;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag as LegacyMethodTag;
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
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class MagicCallPatch implements ClassPatchInterface
{
    /**
     * @var DocBlockFactory|null
     */
    private $docBlockFactory;

    /**
     * @var ContextFactory|null
     */
    private $contextFactory;

    public function __construct()
    {
        if (class_exists('phpDocumentor\Reflection\DocBlockFactory') && class_exists('phpDocumentor\Reflection\Types\ContextFactory')) {
            $this->docBlockFactory = DocBlockFactory::createInstance();
            $this->contextFactory = new ContextFactory();
        }
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

        $tagList = array_merge(
            $this->getClassTagList($reflectionClass),
            $this->getClassInterfacesTagList($reflectionClass)
        );

        foreach($tagList as $tag) {
            /* @var LegacyMethodTag|Method $tag */
            $methodName = $tag->getMethodName();

            if (empty($methodName)) {
                continue;
            }

            if (!$reflectionClass->hasMethod($methodName)) {
                $methodNode = new MethodNode($methodName);
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

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return LegacyTag[]
     */
    private function getClassInterfacesTagList(\ReflectionClass $reflectionClass)
    {
        $interfaces = $reflectionClass->getInterfaces();
        $tagList = array();

        foreach($interfaces as $interface) {
            $tagList = array_merge($tagList, $this->getClassTagList($interface));
        }

        return $tagList;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return LegacyMethodTag[]|Method[]
     */
    private function getClassTagList(\ReflectionClass $reflectionClass)
    {
        try {
            $phpdoc = (null === $this->docBlockFactory || null === $this->contextFactory)
                ? new DocBlock($reflectionClass->getDocComment())
                : $this->docBlockFactory->create($reflectionClass, $this->contextFactory->createFromReflector($reflectionClass))
            ;

            return $phpdoc->getTagsByName('method');
        } catch (\InvalidArgumentException $e) {
            return array();
        }
    }
}

