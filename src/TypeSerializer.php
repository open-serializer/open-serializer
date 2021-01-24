<?php

namespace OpenSerializer;

/**
 * @template T of object
 */
interface TypeSerializer
{
    /**
     * @param T $object
     * @return array<string, mixed>
     */
    public function serializeObject(object $object): array;
}
