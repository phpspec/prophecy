<?php

namespace Tests\Prophecy\Doubler\ClassPatch;

use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\ClassPatch\MagicCallPatch;
use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ClassNode;

class MagicCallPatchTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_classes_with_invalid_tags()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithPhpdocClass');

        $classNode = $this->applyPatchTo($class);

        // Newer phpDocumentor versions allow reading valid method tags, even when some other tags are invalid
        // Some older versions might also have this method due to not considering the method tag invalid as rule evolved, but we don't track that.
        if (class_exists('phpDocumentor\Reflection\DocBlock\Tags\InvalidTag')) {
            $this->assertTrue($classNode->hasMethod('name'));
        }

        // We expect no error when processing the class patch. But we still need to increment the assertion count.
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_supports_arguments_for_magic_methods()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithPhpdocClass');

        $classNode = $this->applyPatchTo($class);

        $this->assertEquals([new ArgumentNode('data')], $classNode->getMethod('__unserialize')->getArguments());
    }

    private function applyPatchTo(\ReflectionClass $class): ClassNode
    {
        $mirror = new ClassMirror();
        $classNode = $mirror->reflect($class, array());

        $patch = new MagicCallPatch();

        $patch->apply($classNode);
        return $classNode;
    }
}
