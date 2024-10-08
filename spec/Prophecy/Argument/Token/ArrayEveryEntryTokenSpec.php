<?php

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\TokenInterface;

class ArrayEveryEntryTokenSpec extends ObjectBehavior
{
    function let(TokenInterface $value)
    {
        $this->beConstructedWith($value);
    }

    function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    function it_holds_value($value)
    {
        $this->getValue()->shouldBe($value);
    }

    function its_string_representation_tells_that_its_an_array_containing_only_value($value)
    {
        $value->__toString()->willReturn('value');
        $this->__toString()->shouldBe('[value, ..., value]');
    }

    function it_wraps_non_token_value_into_ExactValueToken(\stdClass $stdClass)
    {
        $this->beConstructedWith($stdClass);
        $this->getValue()->shouldHaveType('Prophecy\Argument\Token\ExactValueToken');
    }

    function it_does_not_score_if_argument_is_neither_array_nor_traversable()
    {
        $this->scoreArgument('string')->shouldBe(false);
        $this->scoreArgument(new \stdClass())->shouldBe(false);
    }

    function it_does_not_score_empty_array()
    {
        $this->scoreArgument(array())->shouldBe(false);
    }

    function it_does_not_score_traversable_object_without_entries(\Iterator $object)
    {
        (\PHP_VERSION_ID < 80100) ? $object->rewind()->willReturn(null) : $object->rewind()->shouldBeCalled();
        (\PHP_VERSION_ID < 80100) && $object->next()->willReturn(null);
        $object->valid()->willReturn(false);
        $this->scoreArgument($object)->shouldBe(false);
    }

    function it_scores_avg_of_scores_from_value_tokens($value)
    {
        $value->scoreArgument('value1')->willReturn(6);
        $value->scoreArgument('value2')->willReturn(3);
        $this->scoreArgument(array('value1', 'value2'))->shouldBe(4.5);
    }

    function it_scores_false_if_entry_scores_false($value)
    {
        $value->scoreArgument('value1')->willReturn(6);
        $value->scoreArgument('value2')->willReturn(false);
        $this->scoreArgument(array('value1', 'value2'))->shouldBe(false);
    }

    function it_does_not_score_array_keys($value)
    {
        $value->scoreArgument('value')->willReturn(6);
        $value->scoreArgument('key')->shouldNotBeCalled(0);
        $this->scoreArgument(array('key' => 'value'))->shouldBe(6);
    }

    function it_scores_traversable_object_from_value_token(TokenInterface $value, \Iterator $object)
    {
        $object->current()->will(function ($args, $object) {
            $object->valid()->willReturn(false);

            return 'value';
        });
        $object->key()->willReturn('key');
        (\PHP_VERSION_ID < 80100) ? $object->rewind()->willReturn(null) : $object->rewind()->shouldBeCalled();
        (\PHP_VERSION_ID < 80100) ? $object->next()->willReturn(null) : $object->next()->shouldBeCalled();
        $object->valid()->willReturn(true);
        $value->scoreArgument('value')->willReturn(2);
        $this->scoreArgument($object)->shouldBe(2);
    }
}
