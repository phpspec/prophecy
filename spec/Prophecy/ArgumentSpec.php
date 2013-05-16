<?php

namespace spec\Prophecy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArgumentSpec extends ObjectBehavior
{
    function it_has_a_shortcut_for_exact_argument_token()
    {
        $token = $this->exact(42);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ExactValueToken');
        $token->getValue()->shouldReturn(42);
    }

    function it_has_a_shortcut_for_any_argument_token()
    {
        $token = $this->any();
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\AnyValueToken');
    }

    function it_has_a_shortcut_for_multiple_arguments_token()
    {
        $token = $this->cetera();
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\AnyValuesToken');
    }

    function it_has_a_shortcut_for_type_token()
    {
        $token = $this->type('integer');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\TypeToken');
    }

    function it_has_a_shortcut_for_callback_token()
    {
        $token = $this->that('get_class');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\CallbackToken');
    }

    function it_has_a_shortcut_for_object_state_token()
    {
        $token = $this->which('getName', 'everzet');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ObjectStateToken');
    }

    function it_has_a_shortcut_for_logical_and_token()
    {
        $token = $this->allOf('integer', 5);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\LogicalAndToken');
    }
}
