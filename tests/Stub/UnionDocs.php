<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class UnionDocs
{
    /** @var string|array<string> */
    private $stringOrArray;

    /** @param string|array<string> $stringOrArray */
    public function __construct($stringOrArray)
    {
        $this->stringOrArray = $stringOrArray;
    }
}
