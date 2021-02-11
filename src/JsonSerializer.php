<?php declare(strict_types=1);

namespace OpenSerializer;

/**
 * @implements Serializer<JsonObject>
 */
final class JsonSerializer implements Serializer
{
    private ObjectSerializer $serializer;
    private ObjectDeserializer $deserializer;

    public function __construct(?ObjectSerializer $serializer = null, ?ObjectDeserializer $deserializer = null)
    {
        $this->serializer = $serializer ?? new StructSerializer();
        $this->deserializer = $deserializer ?? new StructDeserializer();
    }

    public function serialize(object $object): JsonObject
    {
        return JsonObject::fromArray($this->serializer->serializeObject($object));
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param JsonObject $json
     * @return T
     */
    public function deserialize(string $class, SerializedObject $json): object
    {
        return $this->deserializer->deserializeObject($class, $json->decode());
    }
}
