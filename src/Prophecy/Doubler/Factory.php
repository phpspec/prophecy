<?php

namespace Prophecy\Doubler;

use PhpSpec\Wrapper\Unwrapper;
use Prophecy\Prophet;

/**
 * Instanciates Doubler and attach all registered class patches to it
 **/
class Factory
{
    private $patches = array();

    /**
     * @var array $patches The list of patches to register
     **/
    public function __construct(array $patches = array())
    {
        $this->patches = $patches ?: array(
            new ClassPatch\SplFileInfoPatch,
            new ClassPatch\TraversablePatch,
            new ClassPatch\DisableConstructorPatch,
            new ClassPatch\ProphecySubjectPatch,
            new ClassPatch\ReflectionClassNewInstancePatch,
            new ClassPatch\HhvmExceptionPatch,
            new ClassPatch\MagicCallPatch,
        );
    }

    /**
     * @return Doubler A new instance with all the classPatches
     **/
    public function create()
    {
        $doubler = new Doubler;

        foreach ($this->patches as $patch) {
            $doubler->registerClassPatch($patch);
        }

        return $doubler;
    }
}
