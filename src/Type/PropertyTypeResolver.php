<?php declare(strict_types=1);

namespace OpenSerializer\Type;

use ReflectionClass;
use ReflectionProperty;

interface PropertyTypeResolver
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function resolveType(ReflectionClass $class, ReflectionProperty $property): TypeInfo;
}
