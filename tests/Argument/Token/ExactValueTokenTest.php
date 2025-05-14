<?php

namespace Tests\Prophecy\Argument\Token;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument\Token\ExactValueToken;

class ExactValueTokenTest extends TestCase
{
    /**
     * @see https://github.com/phpspec/prophecy/issues/268
     * @see https://stackoverflow.com/a/19097159/2424814
     */
    #[Test]
    public function does_not_trigger_nesting_error(): void
    {
        $child1 = new ChildClass('A', new ParentClass());
        $child2 = new ChildClass('B', new ParentClass());

        $exactValueToken = new ExactValueToken($child1);
        self::assertEquals(false, $exactValueToken->scoreArgument($child2));
    }

    #[Test]
    public function scores_10_for_objects_with_same_fields(): void
    {
        $child1 = new ChildClass('A', new ParentClass());
        $child2 = new ChildClass('A', new ParentClass());

        $exactValueToken = new ExactValueToken($child1);
        self::assertEquals(10, $exactValueToken->scoreArgument($child2));
    }

    #[Test]
    public function scores_10_for_matching_callables(): void
    {
        $callable = function () {};

        $exactValueToken = new ExactValueToken($callable);
        self::assertEquals(10, $exactValueToken->scoreArgument($callable));
    }

    #[Test]
    public function scores_false_for_object_and_string(): void
    {
        $child1 = new ChildClass('A', new ParentClass());

        $exactValueToken = new ExactValueToken($child1);
        self::assertEquals(false, $exactValueToken->scoreArgument("A"));
    }

    #[Test]
    public function scores_false_for_object_and_int(): void
    {
        $child1 = new ChildClass('A', new ParentClass());

        $exactValueToken = new ExactValueToken($child1);
        self::assertEquals(false, $exactValueToken->scoreArgument(100));
    }

    #[Test]
    public function scores_false_for_object_and_stdclass(): void
    {
        $child1 = new ChildClass('A', new ParentClass());

        $exactValueToken = new ExactValueToken($child1);
        self::assertEquals(false, $exactValueToken->scoreArgument(new \stdClass()));
    }

    #[Test]
    public function scores_false_for_object_and_null(): void
    {
        $child1 = new ChildClass('A', new ParentClass());

        $exactValueToken = new ExactValueToken($child1);
        self::assertEquals(false, $exactValueToken->scoreArgument(null));
    }
}


class ParentClass
{
    public $children = array();

    public function addChild($child)
    {
        $this->children[] = $child;
    }
}

class ChildClass
{
    public $parent = null;
    public $name = null;

    public function __construct($name, $parent)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->parent->addChild($this);
    }

}
