<?php declare(strict_types=1);

namespace OpenSerializer;

use LogicException;
use OpenSerializer\Type\PropertyTypeResolvers;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use function array_key_exists;

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

    private static function assertUserDefined(ReflectionClass $class): void
    {
        if (!$class->isUserDefined()) {
            throw new LogicException("Cannot (de)serialize class {$class->getName()} that is not user defined");
        }
    }

    private function deserializeProperty(ReflectionClass $class, ReflectionProperty $property, $value)
    {
        $type = $this->typeResolver->resolveType($class, $property);

        if ($value === null && $type->isNullable()) {
            return null;
        }

        if ($type->isArray()) {
            $items = [];
            foreach ($value as $key => $item) {
                $items[$key] = $this->deserializeObject($type->type(), $item);
            }

            return $items;
        }

        if ($type->isObject()) {
            return $this->deserializeObject($type->type(), $value);
        }

        if ($type->isScalar()) {
            // TODO cast to proper scalar type
            return $value;
        }

        throw new LogicException("Unable to deserialize property {$property->getName()}");
    }
}
