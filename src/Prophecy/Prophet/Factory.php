<?php

namespace Prophecy\Prophet;

use PhpSpec\Wrapper\Unwrapper;
use Prophecy\Doubler;
use Prophecy\Prophet;

/**
 * Instanciates Prophet with the correct doubler
 **/
class Factory
{
    /**
     * @var Doubler\Factory
     **/
    private $doublerFactory;

    /**
     * @var Unwrapper
     **/
    private $unwrapper;

    /**
     * @param Doubler\Factory $doublerFactory
     **/
    public function __construct(Doubler\Factory $doublerFactory, Unwrapper $unwrapper)
    {
        $this->doublerFactory = $doublerFactory;
        $this->unwrapper = $unwrapper;
    }

    public function create()
    {
         return new Prophet($this->doublerFactory->create(), $this->unwrapper, null);
    }
}
