<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use PhpSpec\ObjectBehavior;

class PropertyNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('title');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('title');
    }

    function it_has_public_visibility_by_default()
    {
        $this->getVisibility()->shouldReturn('public');
    }

    function its_visibility_is_mutable()
    {
        $this->setVisibility('private');
        $this->getVisibility()->shouldReturn('private');
    }

    function it_is_not_static_by_default()
    {
        $this->shouldNotBeStatic();
    }

    function it_should_be_settable_as_static_through_setter()
    {
        $this->setStatic();
        $this->shouldBeStatic();
    }

    function it_accepts_only_supported_visibilities()
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetVisibility('stealth');
    }

    function it_lowercases_visibility_before_setting_it()
    {
        $this->setVisibility('Public');
        $this->getVisibility()->shouldReturn('public');
    }
}
