<?php

namespace Tests\Prophecy\Argument\Token;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument\Token\ExactValueToken;

class ExactValueTokenTest extends TestCase {
	/**
	 * @test
	 * @see https://github.com/phpspec/prophecy/issues/268
	 * @see https://stackoverflow.com/a/19097159/2424814
	 */
	public function does_not_trigger_nesting_error() {
		$child1 = new ChildClass('A', new ParentClass());
		$child2 = new ChildClass('B', new ParentClass());

		$exactValueToken = new ExactValueToken($child1);
		self::assertEquals(false, $exactValueToken->scoreArgument($child2));
	}

	/**
	 * @test
	 */
	public function scores_10_for_objects_with_same_fields() {
		$child1 = new ChildClass('A', new ParentClass());
		$child2 = new ChildClass('A', new ParentClass());

		$exactValueToken = new ExactValueToken($child1);
		self::assertEquals(10, $exactValueToken->scoreArgument($child2));
	}

    /**
     * @test
     */
    public function scores_10_for_matching_callables() {
        $callable = function() {};

        $exactValueToken = new ExactValueToken($callable);
        self::assertEquals(10, $exactValueToken->scoreArgument($callable));
    }

	/**
	 * @test
	 */
	public function scores_false_for_object_and_string() {
		$child1 = new ChildClass('A', new ParentClass());

		$exactValueToken = new ExactValueToken($child1);
		self::assertEquals(false, $exactValueToken->scoreArgument("A"));
	}

	/**
	 * @test
	 */
	public function scores_false_for_object_and_int() {
		$child1 = new ChildClass('A', new ParentClass());

		$exactValueToken = new ExactValueToken($child1);
		self::assertEquals(false, $exactValueToken->scoreArgument(100));
	}

	/**
	 * @test
	 */
	public function scores_false_for_object_and_stdclass() {
		$child1 = new ChildClass('A', new ParentClass());

		$exactValueToken = new ExactValueToken($child1);
		self::assertEquals(false, $exactValueToken->scoreArgument(new \stdClass()));
	}

	/**
	 * @test
	 */
	public function scores_false_for_object_and_null() {
		$child1 = new ChildClass('A', new ParentClass());

		$exactValueToken = new ExactValueToken($child1);
		self::assertEquals(false, $exactValueToken->scoreArgument(null));
	}
}


class ParentClass {

	public $children = array();

	public function addChild($child) {
		$this->children[] = $child;
	}
}

class ChildClass {

	public $parent = null;
	public $name = null;

	public function __construct($name, $parent) {
		$this->name = $name;
		$this->parent = $parent;
		$this->parent->addChild($this);
	}

}
