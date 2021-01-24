<?php declare(strict_types=1);

namespace OpenSerializer\Value;

use LogicException;
use function gettype;
use function is_array;
use function is_object;
use function is_scalar;
use function sprintf;

final class MixedValue
{
    /** @var mixed */
    private $value;

    /** @param mixed $value */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isArray(): bool
    {
        return is_array($this->value);
    }

    /** @return array<int|string,mixed> */
    public function toArray(): array
    {
        if ($this->isArray()) {
            return $this->value;
        }

        throw new LogicException(sprintf('Value %s is not array', gettype($this->value)));
    }

    public function isObject(): bool
    {
        return is_object($this->value);
    }

    public function toObject(): object
    {
        if ($this->isObject()) {
            return $this->value;
        }

        throw new LogicException(sprintf('Value %s is not object', gettype($this->value)));
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }

    public function isScalarOrNull(): bool
    {
        return is_scalar($this->value) || $this->value === null;
    }

    /** @return string|int|float|bool|null */
    public function toScalarOrNull()
    {
        if ($this->isScalarOrNull()) {
            return $this->value;
        }

        throw new LogicException(sprintf('Value %s is not scalar nor null', gettype($this->value)));
    }

    /** @return mixed */
    public function toMixed()
    {
        return $this->value;
    }
}
