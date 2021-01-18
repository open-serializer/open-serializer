<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class IntegersDocs
{
    /** @var int */
    private $intProp;
    /** @var null|int */
    private $nullableIntProp;

    public function __construct(int $intProp, ?int $nullableIntProp)
    {
        $this->intProp = $intProp;
        $this->nullableIntProp = $nullableIntProp;
    }
}
