<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class IntegersDocs
{
    /** @var int */
    private $integer;
    /** @var null|int */
    private $nullableInteger;

    public function __construct(int $integer, ?int $nullableInteger)
    {
        $this->integer = $integer;
        $this->nullableInteger = $nullableInteger;
    }
}
