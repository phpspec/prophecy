<?php

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Exception\InvalidArgumentException;

class InvokeTokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array());
    }

    function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    function it_checks_if_argument_is_a_callback()
    {
        $this->shouldThrow(new InvalidArgumentException(
            'Callable expected as an argument to CallbackToken, but got integer.'
        ))->duringScoreArgument(5);
    }

    function it_scores_argument_as_3_return_value_is_not_checked()
    {
        $this->scoreArgument(function () {})->shouldReturn(3);
    }

    function it_scores_argument_as_3_return_value_correct()
    {
        $this->beConstructedWith(array(), true, 10);

        $this->scoreArgument(function () { return 10; })->shouldReturn(3);
    }

    function it_scores_argument_as_false_return_value_incorrect()
    {
        $this->beConstructedWith(array(), true, 'correct');

        $this->scoreArgument(function () { return 'incorrect'; })->shouldReturn(false);
    }

    function it_invokes_argument(Spy $spy)
    {
        $this->scoreArgument(function () use ($spy) {
            $spy->getWrappedObject()->action();
        });

        $spy->action()->shouldHaveBeenCalled();
    }

    function it_invokes_argument_with_argument_list(Spy $spy)
    {
        $this->beConstructedWith(array(11, 'test'));

        $this->scoreArgument(function ($arg1, $arg2) use ($spy) {
            $spy->getWrappedObject()->actionArgs($arg1, $arg2);
        });

        $spy->actionArgs(11, 'test')->shouldHaveBeenCalled();
    }

    function it_generates_proper_string_representation_for_integer()
    {
        $this->beConstructedWith(array(42));
        $this->__toString()->shouldReturn('invoke(42)');
    }

    function it_generates_proper_string_representation_for_string()
    {
        $this->beConstructedWith(array('some string'));
        $this->__toString()->shouldReturn('invoke("some string")');
    }

    function it_generates_single_line_representation_for_multiline_string()
    {
        $this->beConstructedWith(array("some\nstring"));
        $this->__toString()->shouldReturn('invoke("some\\nstring")');
    }

    function it_generates_proper_string_representation_for_double()
    {
        $this->beConstructedWith(array(42.3));
        $this->__toString()->shouldReturn('invoke(42.3)');
    }

    function it_generates_proper_string_representation_for_boolean_true()
    {
        $this->beConstructedWith(array(true));
        $this->__toString()->shouldReturn('invoke(true)');
    }

    function it_generates_proper_string_representation_for_boolean_false()
    {
        $this->beConstructedWith(array(false));
        $this->__toString()->shouldReturn('invoke(false)');
    }

    function it_generates_proper_string_representation_for_null()
    {
        $this->beConstructedWith(array(null));
        $this->__toString()->shouldReturn('invoke(null)');
    }

    function it_generates_proper_string_representation_for_empty_array()
    {
        $this->beConstructedWith(array(array()));
        $this->__toString()->shouldReturn('invoke([])');
    }

    function it_generates_proper_string_representation_for_array()
    {
        $this->beConstructedWith(array(array('zet', 42)));
        $this->__toString()->shouldReturn('invoke(["zet", 42])');
    }

    function it_generates_proper_string_representation_for_resource()
    {
        $resource = fopen(__FILE__, 'r');
        $this->beConstructedWith(array($resource));
        $this->__toString()->shouldReturn('invoke(stream:'.$resource.')');
    }

    function it_generates_proper_string_representation_for_object($object)
    {
        $objHash = sprintf('%s:%s',
            get_class($object->getWrappedObject()),
            spl_object_hash($object->getWrappedObject())
        );

        $this->beConstructedWith(array($object));
        $this->__toString()->shouldReturn("invoke($objHash Object (\n    'objectProphecy' => Prophecy\Prophecy\ObjectProphecy Object (*Prophecy*)\n))");
    }

    function it_generates_proper_string_representation_for_multiple_arguments()
    {
        $this->beConstructedWith(array(5, 'test', true, false));

        $this->__toString()->shouldReturn('invoke(5, "test", true, false)');
    }

    function it_generates_proper_string_representation_when_result_is_expected()
    {
        $this->beConstructedWith(array(5, 'test', true, false), true, 22);

        $this->__toString()->shouldReturn('22 == invoke(5, "test", true, false)');
    }
}

interface Spy
{
    public function action();

    public function actionArgs($arg1, $arg2);
}
