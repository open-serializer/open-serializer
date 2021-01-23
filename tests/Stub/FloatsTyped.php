<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class FloatsTyped
{
    private float $float;
    private ?float $nullableFloat;

    public function __construct(float $float, ?float $nullableFloat)
    {
        $this->float = $float;
        $this->nullableFloat = $nullableFloat;
    }
}
