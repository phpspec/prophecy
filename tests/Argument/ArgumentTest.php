<?php

namespace Tests\Prophecy\Argument;

use Prophecy\Argument;
use PHPUnit\Framework\TestCase;

class ArgumentTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_true_if_all_values_are_found_in_an_array()
    {
        $token = Argument::containingAllOf('a', 'b');

        $this->assertSame(6.5, $token->scoreArgument(array('a', 'b', 'c')));
    }

    /**
     * @test
     */
    public function it_returns_false_if_a_value_is_missing_from_an_array()
    {
        $token = Argument::containingAllOf('b', 'c');

        $this->assertFalse($token->scoreArgument(array('a', 'b', 'd')));
    }

    /**
     * @test
     */
    public function it_returns_true_if_all_pairs_are_found_in_an_array()
    {
        $token = Argument::withAllEntries(array('b' => 2, 'c' => 3));

        $this->assertSame(8, $token->scoreArgument(array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4)));
    }

    /**
     * @test
     */
    public function it_returns_false_if_pair_is_missing_from_an_array()
    {
        $token = Argument::withAllEntries(array('b' => 2, 'c' => 3));

        $this->assertFalse($token->scoreArgument(array('a' => 1, 'b' => 2, 'd')));
    }
}
