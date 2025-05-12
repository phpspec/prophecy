<?php

namespace Tests\Prophecy\Doubler\ClassPatch;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\ClassPatch\MagicCallPatch;
use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ClassNode;

class MagicCallPatchTest extends TestCase
{
    #[Test]
    public function it_supports_classes_with_invalid_tags(): void
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

    #[Test]
    public function it_supports_arguments_for_magic_methods(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithPhpdocClass');

        $classNode = $this->applyPatchTo($class);

        $method = $classNode->getMethod('__unserialize');
        if (method_exists($method, 'getParameters')) {
            // Reflection Docblock 5.4.0+.
            $args = $method->getParameters();
        } else {
            // Reflection Docblock < 5.4.0.
            $args = $method->getArguments();
        }

        $this->assertEquals([new ArgumentNode('data')], $args);
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
