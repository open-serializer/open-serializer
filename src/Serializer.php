<?php declare(strict_types=1);

namespace OpenSerializer;

/**
 * @template TObjectType of SerializedObject
 */
interface Serializer
{
    /**
     * @return TObjectType
     */
    public function serialize(object $object): SerializedObject;

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param TObjectType $json
     * @return T
     */
    public function deserialize(string $class, SerializedObject $json): object;
}
