<?php declare(strict_types=1);

namespace OpenSerializer;

use OpenSerializer\Deserialize\ReflectingDeserializer;
use Webmozart\Assert\Assert;

final class StructDeserializer implements ObjectDeserializer
{
    private ReflectingDeserializer $default;

    /**
     * @var array<class-string, TypeDeserializer<object>>
     * @psalm-var class-string-map<T, TypeDeserializer<T>>
     */
    private array $custom;

    /**
     * @param array<class-string, TypeDeserializer<object>> $customDeserializers
     * @psalm-param class-string-map<T, TypeDeserializer<T>> $customDeserializers
     */
    public function __construct(array $customDeserializers = [])
    {
        Assert::allIsInstanceOf($customDeserializers, TypeDeserializer::class);
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
        /** @phpstan-var TypeDeserializer<T>|null $customDeserializer */
        $customDeserializer = $this->custom[$class] ?? null;

        if ($customDeserializer !== null) {
            return $customDeserializer->deserializeObject($class, $struct);
        }

        return $this->default->deserializeObject($class, $struct);
    }
}
