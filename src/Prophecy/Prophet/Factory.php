<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Prophet;

use PhpSpec\Wrapper\Unwrapper;
use Prophecy\Doubler;
use Prophecy\Prophet;

/**
 * Instanciates Prophet with the correct doubler
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
class Factory
{
    /**
     * @var Doubler\Factory
     */
    private $doublerFactory;

    /**
     * @var Unwrapper
     */
    private $unwrapper;

    /**
     * @param Doubler\Factory $doublerFactory
     */
    public function __construct(Doubler\Factory $doublerFactory, Unwrapper $unwrapper)
    {
        $this->doublerFactory = $doublerFactory;
        $this->unwrapper = $unwrapper;
    }

    /**
     * @return Prophet A new prophet
     */
    public function create()
    {
         return new Prophet($this->doublerFactory->create(), $this->unwrapper, null);
    }
}
