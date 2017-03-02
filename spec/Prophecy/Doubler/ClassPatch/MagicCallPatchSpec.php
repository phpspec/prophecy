<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

class MagicCallPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function it_supports_anything(ClassNode $node)
    {
        $this->supports($node)->shouldReturn(true);
    }

    function it_discovers_api_using_phpdoc(ClassNode $node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApi');
        $node->getInterfaces()->willReturn(array());

        $node->addMethod(new MethodNode('undefinedMethod'))->shouldBeCalled();

        $this->apply($node);
    }

    function it_ignores_existing_methods(ClassNode $node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApiExtended');
        $node->getInterfaces()->willReturn(array());

        $node->addMethod(new MethodNode('undefinedMethod'))->shouldBeCalled();
        $node->addMethod(new MethodNode('definedMethod'))->shouldNotBeCalled();

        $this->apply($node);
    }

    function it_ignores_empty_methods_from_phpdoc(ClassNode $node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApiInvalidMethodDefinition');
        $node->getInterfaces()->willReturn(array());

        $node->addMethod(new MethodNode(''))->shouldNotBeCalled();

        $this->apply($node);
    }

    function it_discovers_api_using_phpdoc_from_implemented_interfaces(ClassNode $node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApiImplemented');
        $node->getInterfaces()->willReturn(array());

        $node->addMethod(new MethodNode('implementedMethod'))->shouldBeCalled();

        $this->apply($node);
    }

    function it_discovers_api_using_phpdoc_from_own_interfaces(ClassNode $node)
    {
        $node->getParentClass()->willReturn('stdClass');
        $node->getInterfaces()->willReturn(array('spec\Prophecy\Doubler\ClassPatch\MagicalApiImplemented'));

        $node->addMethod(new MethodNode('implementedMethod'))->shouldBeCalled();

        $this->apply($node);
    }

    function it_discovers_api_using_phpdoc_from_extended_parent_interfaces(ClassNode $node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApiImplementedExtended');
        $node->getInterfaces()->willReturn(array());

        $node->addMethod(new MethodNode('implementedMethod'))->shouldBeCalled();

        $this->apply($node);
    }

    function it_has_50_priority()
    {
        $this->getPriority()->shouldReturn(50);
    }
}

/**
 * @method void undefinedMethod()
 */
class MagicalApi
{
    /**
     * @return void
     */
    public function definedMethod()
    {

    }
}

/**
 * @method void invalidMethodDefinition
 * @method void
 * @method
 */
class MagicalApiInvalidMethodDefinition
{
}

/**
 * @method void undefinedMethod()
 * @method void definedMethod()
 */
class MagicalApiExtended extends MagicalApi
{

}

/**
 */
class MagicalApiImplemented implements MagicalApiInterface
{

}

/**
 */
class MagicalApiImplementedExtended extends MagicalApiImplemented
{
}

/**
 * @method void implementedMethod()
 */
interface MagicalApiInterface
{

}
