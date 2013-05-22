<?php

namespace Prophecy\Doubler;

use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\ClassCreator;
use Prophecy\Exception\InvalidArgumentException;

use ReflectionClass;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cached class doubler.
 * Prevents mirroring/creation of the same structure twice.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Doubler
{
    private $mirror;
    private $creator;
    private $namer;
    private $patches = array();

    /**
     * Initializes doubler.
     *
     * @param ClassMirror   $mirror
     * @param ClassCreator  $creator
     * @param NameGenerator $namer
     */
    public function __construct(ClassMirror $mirror = null, ClassCreator $creator = null,
                                NameGenerator $namer = null)
    {
        $this->mirror  = $mirror  ?: new ClassMirror;
        $this->creator = $creator ?: new ClassCreator;
        $this->namer   = $namer   ?: new NameGenerator;
    }

    /**
     * Returns list of registered class patches.
     *
     * @return array
     */
    public function getClassPatches()
    {
        return $this->patches;
    }

    /**
     * Registers new class patch.
     *
     * @param ClassPatch\ClassPatchInterface $patch
     */
    public function registerClassPatch(ClassPatch\ClassPatchInterface $patch)
    {
        $this->patches[] = $patch;

        @usort($this->patches, function($patch1, $patch2) {
            return $patch2->getPriority() - $patch1->getPriority();
        });
    }

    /**
     * Creates double from specific class or/and list of interfaces.
     *
     * @param ReflectionClass $class
     * @param array           $interfaces Array of ReflectionClass instances
     * @param array           $args       Constructor arguments
     *
     * @return DoubleInterface
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function double(ReflectionClass $class = null, array $interfaces, array $args = null)
    {
        foreach ($interfaces as $interface) {
            if (!$interface instanceof ReflectionClass) {
                throw new InvalidArgumentException(sprintf(
                    "[ReflectionClass \$interface1 [, ReflectionClass \$interface2]] array expected as\n".
                    "a second argument to `Doubler::double(...)`, but got %s.",
                    is_object($interface) ? get_class($interface).' class' : gettype($interface)
                ));
            }
        }

        $classname  = $this->createDoubleClass($class, $interfaces);
        $reflection = new ReflectionClass($classname);

        if (null !== $args) {
            return $reflection->newInstanceArgs($args);
        }
        if ((null === $constructor = $reflection->getConstructor()) || $constructor->isPublic()) {
            return $reflection->newInstance();
        }
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            return unserialize(sprintf('O:%d:"%s":0:{}', strlen($classname), $classname));
        }

        return $reflection->newInstanceWithoutConstructor();
    }

    /**
     * Creates double class and returns its FQN.
     *
     * @param ReflectionClass $class
     * @param array           $interfaces
     *
     * @return string
     */
    protected function createDoubleClass(ReflectionClass $class = null, array $interfaces)
    {
        $name = $this->namer->name($class, $interfaces);
        $node = $this->mirror->reflect($class, $interfaces);

        foreach ($this->patches as $patch) {
            if ($patch->supports($node)) {
                $patch->apply($node);
            }
        }

        $this->creator->create($name, $node);

        return $name;
    }
}
