<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class ArrayOfMixed
{
    /** @var array<mixed> */
    private array $items;

    public function __construct(...$items)
    {
        $this->items = $items;
    }
}
