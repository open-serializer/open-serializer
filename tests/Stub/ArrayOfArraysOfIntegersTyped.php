<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class ArrayOfArraysOfIntegersTyped
{
    /** @var array<string, array<IntegersTyped>> */
    private array $items;

    /**
     * @param array<string, array<IntegersTyped>> $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }
}
