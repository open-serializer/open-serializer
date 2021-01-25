<?php declare(strict_types=1);

namespace OpenSerializer;

interface SerializedObject
{
    public function toString(): string;
}
