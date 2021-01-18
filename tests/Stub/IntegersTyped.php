<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class IntegersTyped
{
    private int $intProp;
    private ?int $nullableIntProp;

    public function __construct(int $intProp, ?int $nullableIntProp)
    {
        $this->intProp = $intProp;
        $this->nullableIntProp = $nullableIntProp;
    }
}
