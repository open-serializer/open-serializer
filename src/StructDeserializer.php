<?php declare(strict_types=1);

namespace OpenSerializer;

use OpenSerializer\Deserialize\ReflectingDeserializer;

final class StructDeserializer implements ObjectDeserializer
{
    private ReflectingDeserializer $default;

    /**
     * @var array<class-string, CustomDeserializer<object>>
     * @psalm-var class-string-map<T, CustomDeserializer<T>>
     */
    private array $custom;

    /**
     * @param array<class-string, CustomDeserializer<object>> $customDeserializers
     * @psalm-param class-string-map<T, CustomDeserializer<T>> $customDeserializers
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
        /** @phpstan-var CustomDeserializer<T>|null $customDeserializer */
        $customDeserializer = $this->custom[$class] ?? null;

        if ($customDeserializer !== null) {
            return $customDeserializer->deserializeObject($class, $struct);
        }

        return $this->default->deserializeObject($class, $struct);
    }
}
