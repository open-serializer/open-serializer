<?php declare(strict_types=1);

namespace OpenSerializer\Type;

use function in_array;

final class TypeInfo
{
    public const SCALAR_TYPES = ['int', 'float', 'string', 'bool'];

    private string $type;
    private bool $isArray;
    private bool $isObject;
    private bool $isNullable;

    private function __construct(string $type, bool $isNullable, bool $isArray, bool $isObject)
    {
        $this->type = $type;
        $this->isNullable = $isNullable;
        $this->isArray = $isArray;
        $this->isObject = $isObject;
    }

    public static function ofMixed(): self
    {
        return new self('mixed', true, false, false);
    }

    public static function ofBuiltIn(string $type, bool $isNullable): self
    {
        return new self($type, $isNullable, false, false);
    }

    public static function ofObject(string $type, bool $isNullable): self
    {
        return new self($type, $isNullable, false, true);
    }

    public static function ofTypedArray(string $type, bool $isNullable): self
    {
        return new self($type, $isNullable, true, false);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }

    public function isObject(): bool
    {
        return $this->isObject;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function isScalar(): bool
    {
        return in_array($this->type, self::SCALAR_TYPES);
    }

    public function isStrict(): bool
    {
        return $this->isScalar()
            || $this->isObject
            || $this->isArray && $this->type !== 'mixed';
    }

    public function isMixed(): bool
    {
        return $this->type === 'mixed';
    }
}
