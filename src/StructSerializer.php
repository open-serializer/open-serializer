<?php declare(strict_types=1);

namespace OpenSerializer;

use OpenSerializer\Serialize\ReflectingSerializer;
use function get_class;

final class StructSerializer implements ObjectSerializer
{
    private ObjectSerializer $default;
    /** @var array<class-string, ObjectSerializer> */
    private array $custom;

    /** @param array<class-string, ObjectSerializer> $customSerializers */
    public function __construct(array $customSerializers = [])
    {
        $this->default = new ReflectingSerializer($this);
        $this->custom = $customSerializers;
    }

    /** @return array<string, mixed> */
    public function serializeObject(object $object): array
    {
        return ($this->custom[get_class($object)] ?? $this->default)->serializeObject($object);
    }
}
