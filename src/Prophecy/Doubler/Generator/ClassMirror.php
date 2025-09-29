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

use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\Doubler\Generator\Node\Type\BuiltinType;
use Prophecy\Doubler\Generator\Node\Type\IntersectionType;
use Prophecy\Doubler\Generator\Node\Type\ObjectType;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Doubler\ClassMirrorException;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

/**
 * Class mirror.
 * Core doubler class. Mirrors specific class and/or interfaces into class node tree.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ClassMirror
{
    private const REFLECTABLE_METHODS = array(
        '__construct',
        '__destruct',
        '__sleep',
        '__wakeup',
        '__toString',
        '__call',
        '__invoke',
    );

    /**
     * Reflects provided arguments into class node.
     *
     * @param ReflectionClass<object>|null $class
     * @param ReflectionClass<object>[]    $interfaces
     *
     * @return Node\ClassNode
     *
     */
    public function reflect(?ReflectionClass $class, array $interfaces)
    {
        $node = new Node\ClassNode();

        if (null !== $class) {
            if (true === $class->isInterface()) {
                throw new InvalidArgumentException(sprintf(
                    "Could not reflect %s as a class, because it\n"
                    ."is interface - use the second argument instead.",
                    $class->getName()
                ));
            }

            $this->reflectClassToNode($class, $node);
        }

        foreach ($interfaces as $interface) {
            if (!$interface instanceof ReflectionClass) {
                throw new InvalidArgumentException(sprintf(
                    "[ReflectionClass \$interface1 [, ReflectionClass \$interface2]] array expected as\n"
                    ."a second argument to `ClassMirror::reflect(...)`, but got %s.",
                    is_object($interface) ? get_class($interface).' class' : gettype($interface)
                ));
            }
            if (false === $interface->isInterface()) {
                throw new InvalidArgumentException(sprintf(
                    "Could not reflect %s as an interface, because it\n"
                    ."is class - use the first argument instead.",
                    $interface->getName()
                ));
            }

            $this->reflectInterfaceToNode($interface, $node);
        }

        $node->addInterface('Prophecy\Doubler\Generator\ReflectionInterface');

        return $node;
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private function reflectClassToNode(ReflectionClass $class, Node\ClassNode $node): void
    {
        if (true === $class->isFinal()) {
            throw new ClassMirrorException(sprintf(
                'Could not reflect class %s as it is marked final.', $class->getName()
            ), $class);
        }

        if (method_exists(ReflectionClass::class, 'isReadOnly')) {
            $node->setReadOnly($class->isReadOnly());
        }

        $node->setParentClass($class->getName());

        foreach ($class->getMethods(ReflectionMethod::IS_ABSTRACT) as $method) {
            if (false === $method->isProtected()) {
                continue;
            }

            $this->reflectMethodToNode($method, $node);
        }

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (0 === strpos($method->getName(), '_')
                && !in_array($method->getName(), self::REFLECTABLE_METHODS)) {
                continue;
            }

            if (true === $method->isFinal()) {
                $node->addUnextendableMethod($method->getName());
                continue;
            }

            $this->reflectMethodToNode($method, $node);
        }
    }

    /**
     * @param ReflectionClass<object> $interface
     */
    private function reflectInterfaceToNode(ReflectionClass $interface, Node\ClassNode $node): void
    {
        $node->addInterface($interface->getName());

        foreach ($interface->getMethods() as $method) {
            $this->reflectMethodToNode($method, $node);
        }
    }

    private function reflectMethodToNode(ReflectionMethod $method, Node\ClassNode $classNode): void
    {
        $node = new Node\MethodNode($method->getName());

        if (true === $method->isProtected()) {
            $node->setVisibility('protected');
        }

        if (true === $method->isStatic()) {
            $node->setStatic();
        }

        if (true === $method->returnsReference()) {
            $node->setReturnsReference();
        }

        $returnReflectionType = null;
        if ($method->hasReturnType()) {
            $returnReflectionType = $method->getReturnType();
        } elseif (method_exists($method, 'hasTentativeReturnType') && $method->hasTentativeReturnType()) {
            // Tentative return types also need reflection
            $returnReflectionType = $method->getTentativeReturnType();
        }

        if (null !== $returnReflectionType) {
            $returnType = $this->createTypeFromReflection(
                $returnReflectionType,
                $method->getDeclaringClass()
            );
            $node->setReturnTypeNode(new ReturnTypeNode($returnType));
        }

        if (is_array($params = $method->getParameters()) && count($params)) {
            foreach ($params as $param) {
                $this->reflectArgumentToNode($param, $method->getDeclaringClass(), $node);
            }
        }

        $classNode->addMethod($node);
    }

    /**
     * @param ReflectionClass<object> $declaringClass
     *
     * @return void
     */
    private function reflectArgumentToNode(ReflectionParameter $parameter, ReflectionClass $declaringClass, Node\MethodNode $methodNode): void
    {
        $name = $parameter->getName() == '...' ? '__dot_dot_dot__' : $parameter->getName();
        $node = new Node\ArgumentNode($name);

        $refType = $parameter->getType();
        if (null !== $refType) {
            $typeHint = $this->createTypeFromReflection($refType, $declaringClass);
            $node->setTypeNode(new ArgumentTypeNode($typeHint));
        }

        if ($parameter->isVariadic()) {
            $node->setAsVariadic();
        }

        if ($this->hasDefaultValue($parameter)) {
            $node->setDefault($this->getDefaultValue($parameter));
        }

        if ($parameter->isPassedByReference()) {
            $node->setAsPassedByReference();
        }

        $methodNode->addArgument($node);
    }

    private function hasDefaultValue(ReflectionParameter $parameter): bool
    {
        if ($parameter->isVariadic()) {
            return false;
        }

        if ($parameter->isDefaultValueAvailable()) {
            return true;
        }

        return $parameter->isOptional();
    }

    /**
     * @return mixed
     */
    private function getDefaultValue(ReflectionParameter $parameter)
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return null;
        }

        return $parameter->getDefaultValue();
    }

    /**
     * @param ReflectionClass<object> $declaringClass Context reflection class
     */
    private function createTypeFromReflection(ReflectionType $type, ReflectionClass $declaringClass): TypeInterface
    {
        if ($type instanceof ReflectionIntersectionType) {
            $innerTypes = [];
            /** @var ReflectionNamedType $innerReflectionType */
            foreach ($type->getTypes() as $innerReflectionType) {
                // Intersections cannot be composed of builtin types
                /** @var class-string $objectType */
                $objectType = $innerReflectionType->getName();
                $innerTypes[] = new ObjectType($objectType);
            }
            return new IntersectionType($innerTypes);
        }

        if ($type instanceof ReflectionUnionType) {
            $innerTypes = [];
            /** @var ReflectionIntersectionType|ReflectionNamedType $innerReflectionType */
            foreach ($type->getTypes() as $innerReflectionType) {
                if ($innerReflectionType instanceof ReflectionIntersectionType) {
                    /** @var IntersectionType $intersection */
                    $intersection = $this->createTypeFromReflection($innerReflectionType, $declaringClass);
                    $innerTypes[] = $intersection;
                    continue;
                }
                $name = $this->resolveTypeName($innerReflectionType->getName(), $declaringClass);
                if ($innerReflectionType->isBuiltin() || $name === 'static') {
                    $innerTypes[] = new BuiltinType($name);
                } elseif ($name === 'self') {
                    $innerTypes[] = new ObjectType($declaringClass->getName());
                } else {
                    /** @var class-string $name */
                    $innerTypes[] = new ObjectType($name);
                }
            }
            // Nullability is handled by 'null' being one of the types in the union
            return new UnionType($innerTypes);
        }

        // Handle Named Types (single types like int, string, MyClass, ?MyClass)
        if ($type instanceof ReflectionNamedType) {
            $name = $this->resolveTypeName($type->getName(), $declaringClass);
            if ($type->isBuiltin() || $name === 'static') {
                $simpleType = new BuiltinType($name); // SimpleType constructor normalizes
            } else {
                /** @var class-string $name */
                $simpleType = new ObjectType($name);
            }

            // Handle nullability for named types explicitly by wrapping in a UnionType if needed
            if ($type->allowsNull() && $name !== 'mixed' && $name !== 'null') {
                return new UnionType([new BuiltinType('null'), $simpleType]);
            }

            return $simpleType;
        }

        // Unknown ReflectionType implementation
        throw new ClassMirrorException('Unknown reflection type: '.get_class($type), $declaringClass);
    }

    /**
     * @param ReflectionClass<object> $contextClass
     */
    private function resolveTypeName(string $name, \ReflectionClass $contextClass): string
    {
        if ($name === 'self') {
            return $contextClass->getName();
        }
        if ($name === 'parent') {
            $parent = $contextClass->getParentClass();
            if (false === $parent) {
                throw new ClassMirrorException(sprintf('Cannot use "parent" type hint in class "%s" as it does not have a parent.', $contextClass->getName()), $contextClass);
            }
            return $parent->getName();
        }

        return $name;
    }
}
