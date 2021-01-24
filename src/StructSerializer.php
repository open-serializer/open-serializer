<?php declare(strict_types=1);

namespace OpenSerializer;

use OpenSerializer\Serialize\ReflectingSerializer;
use Webmozart\Assert\Assert;
use function get_class;

final class StructSerializer implements ObjectSerializer
{
    private ObjectSerializer $default;

    /**
     * @psalm-var class-string-map<T, TypeSerializer<T>>
     * @var array<class-string, TypeSerializer<object>>
     */
    private array $custom;

    /**
     * @psalm-param class-string-map<T, TypeSerializer<T>> $customSerializers
     * @param array<class-string, TypeSerializer<object>> $customSerializers
     */
    public function __construct(array $customSerializers = [])
    {
        Assert::allIsInstanceOf($customSerializers, TypeSerializer::class);
        $this->default = new ReflectingSerializer($this);
        $this->custom = $customSerializers;
    }

    /** @return array<string, mixed> */
    public function serializeObject(object $object): array
    {
        return ($this->custom[get_class($object)] ?? $this->default)->serializeObject($object);
    }
}
