<?php declare(strict_types=1);

namespace OpenSerializer\Deserialize;

use Error;
use LogicException;
use OpenSerializer\ObjectDeserializer;
use OpenSerializer\Type\PropertyTypeResolvers;
use OpenSerializer\Type\TypeInfo;
use OpenSerializer\Value\MixedValue;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use function array_key_exists;
use function class_exists;

final class ReflectingDeserializer implements ObjectDeserializer
{
    private PropertyTypeResolvers $typeResolver;
    private ObjectDeserializer $next;

    public function __construct(ObjectDeserializer $next)
    {
        $this->typeResolver = PropertyTypeResolvers::default();
        $this->next = $next;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string|int, mixed> $struct
     * @return T
     */
    public function deserializeObject(string $class, array $struct): object
    {
        try {
            $classReflection = new ReflectionClass($class);
        } catch (ReflectionException| Error $reflectionException) {
            throw new LogicException("Cannot reflect {$class}", 0, $reflectionException);
        }

        $this->assertCanBeCreated($classReflection);

        try {
            $object = $classReflection->newInstanceWithoutConstructor();
        } catch (ReflectionException $reflectionException) {
            throw new LogicException("Cannot create new {$class}", 0, $reflectionException);
        }

        foreach ($classReflection->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $struct)) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue(
                $object,
                $this->deserializeProperty($classReflection, $property, new MixedValue($struct[$property->getName()]))
            );
        }

        return $object;
    }

    /**
     * @param ReflectionClass<object> $class
     * @return mixed
     */
    private function deserializeProperty(ReflectionClass $class, ReflectionProperty $property, MixedValue $value)
    {
        $type = $this->typeResolver->resolveType($class, $property);

        return $this->deserializeValue($type, $value);
    }

    /**
     * @param TypeInfo $type
     * @return mixed
     */
    private function deserializeValue(TypeInfo $type, MixedValue $value)
    {
        if ($value->isNull() && $type->isNullable()) {
            return null;
        }

        if ($type->isArray()) {
            $items = [];
            foreach ($value->toArray() as $key => $item) {
                $items[$key] = $this->deserializeValue($type->innerType(), new MixedValue($item));
            }

            return $items;
        }

        if (class_exists($type->type())) {
            return $this->next->deserializeObject($type->type(), $value->toArray());
        }

        if ($type->isString()) {
            return (string)$value->toScalarOrNull();
        }

        if ($type->isInteger()) {
            return (int)$value->toScalarOrNull();
        }

        if ($type->isFloat()) {
            return (float)$value->toScalarOrNull();
        }

        if ($type->isBoolean()) {
            return (bool)$value->toScalarOrNull();
        }

        if ($type->isMixed()) {
            return $value->toMixed();
        }

        throw new LogicException("Unable to deserialize property");
    }

    private function assertCanBeCreated(ReflectionClass $class): void
    {
        if (!$class->isUserDefined()) {
            throw new LogicException("Cannot deserialize class {$class->getName()} that is not user defined");
        }

        if ($class->isInterface()) {
            throw new LogicException("Cannot deserialize interface {$class->getName()}");
        }
    }
}
