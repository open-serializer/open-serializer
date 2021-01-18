<?php declare(strict_types=1);

namespace OpenSerializer;

use LogicException;
use OpenSerializer\Type\PropertyTypeResolvers;
use OpenSerializer\Type\TypeInfo;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use function array_key_exists;
use function class_exists;

final class StructDeserializer
{
    public const SCALAR_TYPES = ['int', 'float', 'string', 'bool'];

    private PropertyTypeResolvers $typeResolver;

    public function __construct()
    {
        $this->typeResolver = PropertyTypeResolvers::default();
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $struct
     * @return T
     */
    public function deserializeObject(string $class, array $struct): object
    {
        try {
            $classReflection = new ReflectionClass($class);
        } catch (ReflectionException $reflectionException) {
            throw new LogicException("Cannot create new {$class}", 0, $reflectionException);
        }

        self::assertUserDefined($classReflection);
        $object = $classReflection->newInstanceWithoutConstructor();

        foreach ($classReflection->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $struct)) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue(
                $object,
                $this->deserializeProperty($classReflection, $property, $struct[$property->getName()])
            );
        }

        return $object;
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private static function assertUserDefined(ReflectionClass $class): void
    {
        if (!$class->isUserDefined()) {
            throw new LogicException("Cannot (de)serialize class {$class->getName()} that is not user defined");
        }
    }

    /**
     * @param ReflectionClass<object> $class
     * @param mixed $value
     * @return mixed
     */
    private function deserializeProperty(ReflectionClass $class, ReflectionProperty $property, $value)
    {
        $type = $this->typeResolver->resolveType($class, $property);

        return $this->deserializeValue($type, $value);
    }

    /**
     * @param TypeInfo $type
     * @param mixed $value
     * @return mixed
     */
    private function deserializeValue(TypeInfo $type, $value)
    {
        if ($value === null && $type->isNullable()) {
            return null;
        }

        if ($type->isArray()) {
            $items = [];
            foreach ($value as $key => $item) {
                $items[$key] = $this->deserializeValue($type->innerType(), $item);
            }

            return $items;
        }

        if (class_exists($type->type())) {
            return $this->deserializeObject($type->type(), $value);
        }

        if ($type->isString()) {
            return (string)$value;
        }

        if ($type->isInteger()) {
            return (int)$value;
        }

        if ($type->isFloat()) {
            return (float)$value;
        }

        if ($type->isBoolean()) {
            return (bool)$value;
        }

        if ($type->isMixed()) {
            return $value;
        }

        throw new LogicException("Unable to deserialize property");
    }
}
