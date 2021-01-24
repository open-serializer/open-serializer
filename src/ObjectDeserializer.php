<?php

namespace OpenSerializer;

interface ObjectDeserializer
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string|int, mixed> $struct
     * @return T
     */
    public function deserializeObject(string $class, array $struct): object;
}
