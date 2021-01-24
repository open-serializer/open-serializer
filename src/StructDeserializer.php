<?php declare(strict_types=1);

namespace OpenSerializer;

use OpenSerializer\Deserialize\ReflectingDeserializer;

final class StructDeserializer implements ObjectDeserializer
{
    private ReflectingDeserializer $default;

    /**
     * @var array<class-string, ObjectDeserializer>
     * @psalm-var class-string-map<T, ObjectDeserializer<T>>
     */
    private array $custom;

    /**
     * @param array<class-string, ObjectDeserializer> $customDeserializers
     * @psalm-param class-string-map<T, ObjectDeserializer<T>> $customDeserializers
     */
    public function __construct(array $customDeserializers = [])
    {
        $this->default = new ReflectingDeserializer($this);
        $this->custom = $customDeserializers;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string|int, mixed> $struct
     * @return T
     */
    public function deserializeObject(string $class, array $struct): object
    {
        return ($this->custom[$class] ?? $this->default)->deserializeObject($class, $struct);
    }
}
