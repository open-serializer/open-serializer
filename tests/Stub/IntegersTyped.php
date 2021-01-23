<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class IntegersTyped
{
    private int $integer;
    private ?int $nullableInteger;

    public function __construct(int $integer, ?int $nullableInteger)
    {
        $this->integer = $integer;
        $this->nullableInteger = $nullableInteger;
    }
}
