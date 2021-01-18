<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class GenericArrays
{
    /** @var array<IntegersTyped> */
    private array $listOfIntegers;

    /** @param array<IntegersTyped> $listOfIntegers */
    public function __construct(array $listOfIntegers)
    {
        $this->listOfIntegers = $listOfIntegers;
    }
}
