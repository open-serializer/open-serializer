<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class UnknownType
{
    /** @var TheUnknown */
    private $unknown;

    public function __construct(TheUnknown $unknown)
    {
        $this->unknown = $unknown;
    }
}
