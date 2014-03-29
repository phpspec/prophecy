<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler;

use PhpSpec\Wrapper\Unwrapper;
use Prophecy\Prophet;

/**
 * Instanciates Doubler and attach all registered class patches to it
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
class Factory
{
    private $patches = array();

    /**
     * @param ClassPatchInterface[] $patches The list of patches to register in addition to default ones
     */
    public function __construct(array $patches = array())
    {
        $patches = array_merge(array(
            new ClassPatch\SplFileInfoPatch,
            new ClassPatch\TraversablePatch,
            new ClassPatch\DisableConstructorPatch,
            new ClassPatch\ProphecySubjectPatch,
            new ClassPatch\ReflectionClassNewInstancePatch,
            new ClassPatch\HhvmExceptionPatch,
            new ClassPatch\MagicCallPatch,
        ), $patches);

        $this->patches = array_combine(array_map('get_class', $patches), $patches);
    }

    /**
     * Removes a class patch from the register
     *
     * @param string $className The class name of the class patch to remove
     */
    public function removeClassPatch($className)
    {
        unset($this->patches[$className]);
    }

    /**
     * @return Doubler A new instance with all the class patches
     */
    public function create()
    {
        $doubler = new Doubler;

        foreach ($this->patches as $patch) {
            $doubler->registerClassPatch($patch);
        }

        return $doubler;
    }
}
