<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator;

use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\Doubler\Generator\Node\TypeNodeAbstract;

/**
 * Class code creator.
 * Generates PHP code for specific class node tree.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ClassCodeGenerator
{
    // Used to accept an optional first argument with the deprecated Prophecy\Doubler\Generator\TypeHintReference so careful when adding a new argument in a minor version.
    public function __construct()
    {
    }

    /**
     * Generates PHP code for class node.
     *
     * @param string         $classname
     * @param Node\ClassNode $class
     *
     * @return string
     */
    public function generate($classname, Node\ClassNode $class)
    {
        $parts     = explode('\\', $classname);
        $classname = array_pop($parts);
        $namespace = implode('\\', $parts);

        $code = sprintf("%sclass %s extends \%s implements %s {\n",
            $class->isReadOnly() ? 'readonly ': '',
            $classname,
            $class->getParentClass(),
            implode(', ',
                array_map(function ($interface) {return '\\'.$interface;}, $class->getInterfaces())
            )
        );

        foreach ($class->getProperties() as $name => $visibility) {
            $code .= sprintf("%s \$%s;\n", $visibility, $name);
        }
        $code .= "\n";

        foreach ($class->getMethods() as $method) {
            $code .= $this->generateMethod($method)."\n";
        }
        $code .= "\n}";

        return sprintf("namespace %s {\n%s\n}", $namespace, $code);
    }

    private function generateMethod(Node\MethodNode $method): string
    {
        $php = sprintf("%s %s function %s%s(%s)%s {\n",
            $method->getVisibility(),
            $method->isStatic() ? 'static' : '',
            $method->returnsReference() ? '&':'',
            $method->getName(),
            implode(', ', $this->generateArguments($method->getArguments())),
            ($ret = $this->generateTypes($method->getReturnTypeNode())) ? ': '.$ret : ''
        );
        $php .= $method->getCode()."\n";

        return $php.'}';
    }

    private function generateTypes(TypeNodeAbstract $typeNode): string
    {
        if (!$typeNode->getTypes()) {
            return '';
        }

        // When we require PHP 8 we can stop generating ?foo nullables and remove this first block
        if ($typeNode->canUseNullShorthand()) {
            return sprintf( '?%s', $typeNode->getNonNullTypes()[0]);
        } else {
            return join('|', $typeNode->getTypes());
        }
    }

    /**
     * @param list<Node\ArgumentNode> $arguments
     *
     * @return list<string>
     */
    private function generateArguments(array $arguments): array
    {
        return array_map(function (Node\ArgumentNode $argument){

            $php = $this->generateTypes($argument->getTypeNode());

            $php .= ' '.($argument->isPassedByReference() ? '&' : '');

            $php .= $argument->isVariadic() ? '...' : '';

            $php .= '$'.$argument->getName();

            if ($argument->isOptional() && !$argument->isVariadic()) {
                $php .= ' = '.var_export($argument->getDefault(), true);
            }

            return $php;
        }, $arguments);
    }
}
