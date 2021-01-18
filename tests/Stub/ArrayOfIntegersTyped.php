<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class ArrayOfIntegersTyped
{
    /** @var array<IntegersTyped> */
    private array $items;

    public function __construct(IntegersTyped ...$items)
    {
        $this->items = $items;
    }
}
