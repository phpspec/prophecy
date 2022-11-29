<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Exception\Doubler\DoubleException;

class ArgumentTypeNodeSpec extends ObjectBehavior
{
    function it_has_no_types_at_start()
    {
        $this->getTypes()->shouldReturn([]);
    }

    function it_can_have_a_simple_type()
    {
        $this->beConstructedWith('int');

        $this->getTypes()->shouldReturn(['int']);
    }

    function it_can_have_multiple_types()
    {
        $this->beConstructedWith('int', 'string');

        $this->getTypes()->shouldReturn(['int', 'string']);
    }

    function it_will_prefix_fcqns()
    {
        $this->beConstructedWith('Foo');

        $this->getTypes()->shouldReturn(['\\Foo']);
    }

    function it_will_not_prefix_fcqns_that_already_have_prefix()
    {
        $this->beConstructedWith('\\Foo');

        $this->getTypes()->shouldReturn(['\\Foo']);
    }

    function it_can_use_shorthand_null_syntax_if_it_has_single_type_plus_null()
    {
        $this->beConstructedWith('int', 'null');

        $this->canUseNullShorthand()->shouldReturn(true);
    }

    function it_can_not_use_shorthand_null_syntax_if_it_does_not_allow_null()
    {
        $this->beConstructedWith('int');

        $this->canUseNullShorthand()->shouldReturn(false);
    }

    function it_can_not_use_shorthand_null_syntax_if_it_has_more_than_one_non_null_type()
    {
        $this->beConstructedWith('int', 'string', 'null');

        $this->canUseNullShorthand()->shouldReturn(false);
    }

    function it_can_return_non_null_types()
    {
        $this->beConstructedWith('int', 'null');

        $this->getNonNullTypes()->shouldReturn(['int']);
    }

    function it_does_not_allow_union_mixed()
    {
        $this->beConstructedWith('mixed', 'int');

        if (PHP_VERSION_ID >=80000) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }
    }

    function it_does_not_prefix_false()
    {
        $this->beConstructedWith('false', 'array');

        $this->getTypes()->shouldReturn(['false', 'array']);
    }

    function it_allows_standalone_false()
    {
        $this->beConstructedWith('false');

        if (PHP_VERSION_ID >=80000 && PHP_VERSION_ID < 80200) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }

        if (PHP_VERSION_ID >= 80200) {
            $this->getTypes()->shouldReturn(['false']);
        }
    }

    function it_allows_standalone_null()
    {
        $this->beConstructedWith('null');

        if (PHP_VERSION_ID >=80000 && PHP_VERSION_ID < 80200) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }

        if (PHP_VERSION_ID >= 80200) {
            $this->getTypes()->shouldReturn(['null']);
        }
    }

    function it_allows_standalone_true()
    {
        $this->beConstructedWith('true');

        if (PHP_VERSION_ID >=80000 && PHP_VERSION_ID < 80200) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }

        if (PHP_VERSION_ID >= 80200) {
            $this->getTypes()->shouldReturn(['true']);
        }
    }

    function it_allows_nullable_false()
    {
        $this->beConstructedWith('null', 'false');

        if (PHP_VERSION_ID >=80000 && PHP_VERSION_ID < 80200) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }

        if (PHP_VERSION_ID >= 80200) {
            $this->getTypes()->shouldReturn(['null', 'false']);
        }
    }

    function it_allows_nullable_true()
    {
        $this->beConstructedWith('null', 'true');

        if (PHP_VERSION_ID >=80000 && PHP_VERSION_ID < 80200) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }

        if (PHP_VERSION_ID >= 80200) {
            $this->getTypes()->shouldReturn(['null', 'true']);
        }
    }

    function it_allows_union_with_false()
    {
        $this->beConstructedWith('false', 'Foo');

        if (PHP_VERSION_ID >= 80000) {
            $this->getTypes()->shouldReturn(['false', '\\Foo']);
        }
    }
}
