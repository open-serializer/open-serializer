<?php

namespace OpenSerializer;

interface ObjectSerializer
{
    /** @return array<string, mixed> */
    public function serializeObject(object $object): array;
}
