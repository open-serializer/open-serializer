<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class ArrayOfIntegers
{
    /** @var array<int> */
    private array $items;

    public function __construct(int ...$items)
    {
        $this->items = $items;
    }
}
