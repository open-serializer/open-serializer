<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class ArrayOfUnknown
{
    private array $items;

    public function __construct(...$items)
    {
        $this->items = $items;
    }
}
