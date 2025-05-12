<?php

namespace Tests\Prophecy\Doubler\Generator\Node;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\Doubler\Generator\Node\TypeNodeAbstract;

class TypeNodeAbstractTest extends TestCase
{
    /**
     * @return \Generator<array{0: TypeNodeAbstract, 1?: bool}>
     */
    public static function childClassDataProvider(): \Generator
    {
        $typesCombination = [
            ['bool', 'null'],
            ['int', 'bool', 'null'],
        ];

        if (PHP_VERSION_ID >= 80200) {
            $typesCombination[] = ['null'];
        }

        foreach ($typesCombination as $types) {
            $count = count($types);
            $expected = $count === 2;

            yield $count.' return types' => [new ReturnTypeNode(...$types), $expected];
            yield $count.' argument types' => [new ArgumentTypeNode(...$types), $expected];
        }
    }

    #[DataProvider('childClassDataProvider')]
    #[Test]
    public function it_can_use_null_shorthand_only_with_two_types(TypeNodeAbstract $node, bool $expected): void
    {
        $this->assertSame($expected, $node->canUseNullShorthand());
    }
}
