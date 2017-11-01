<?php

namespace Tests\Prophecy\Promise;

/**
 * @internal
 */
final class CallbackPromiseTest extends \PHPUnit_Framework_TestCase
{
    public function testCallbackPromise()
    {
        $fixer = $this->prophesize('Tests\Prophecy\Promise\CallbackPromiseTestDummy');
        $fixer->getName()->will(static function (array $arguments) use (&$things) {
            return $things
                ? 'yes'
                : 'no'
            ;
        });

        $this->assertSame('no', $fixer->reveal()->getName());
    }
}

class CallbackPromiseTestDummy
{
    public function getName() {
        return 'dummy';
    }
}
