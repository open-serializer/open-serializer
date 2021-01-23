<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class FloatsDocs
{
    /** @var float */
    private $float;
    /** @var null|float */
    private $nullableFloat;

    public function __construct(float $float, ?float $nullableFloat)
    {
        $this->float = $float;
        $this->nullableFloat = $nullableFloat;
    }
}
