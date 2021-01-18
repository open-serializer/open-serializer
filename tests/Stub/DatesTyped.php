<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

use DateTimeImmutable;

final class DatesTyped
{
    private DateTimeImmutable $date;

    public function __construct(DateTimeImmutable $date)
    {
        $this->date = $date;
    }
}
