<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class ScalarUnion
{
    /** @var int|float|string|bool */
    private $scalar;

    /** @param int|float|string|bool $scalar */
    public function __construct($scalar)
    {
        $this->scalar = $scalar;
    }
}
