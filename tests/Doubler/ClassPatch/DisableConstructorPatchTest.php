<?php

namespace Tests\Prophecy\Doubler\ClassPatch;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\ClassPatch\DisableConstructorPatch;
use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\PhpUnit\ProphecyTrait;

class DisableConstructorPatchTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @see https://github.com/phpspec/prophecy/issues/555
     */
    public function testArgumentsForConstructorMethodArePassedToParent()
    {
        $array = ['key' => 'value'];
        $arrayObject = new ArrayObject($array);

        $prophecy = $this->prophesize(\Fixtures\Prophecy\ConstructorArguments::class);
        $prophecy->willBeConstructedWith([$arrayObject, $array]);
        $subject = $prophecy->reveal();

        $this->assertSame($arrayObject, $subject->arg_1);
        $this->assertSame($array, $subject->arg_2);
        $this->assertNull($subject->arg_3);
    }
}
