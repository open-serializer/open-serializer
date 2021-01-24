<?php

namespace OpenSerializer;

/**
 * @template T of object
 */
interface CustomDeserializer
{
    /**
     * @param class-string<T> $class
     * @param array<string|int, mixed> $struct
     * @return T
     */
    public function deserializeObject(string $class, array $struct): object;
}
