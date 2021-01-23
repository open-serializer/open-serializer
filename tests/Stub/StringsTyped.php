<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class StringsTyped
{
    private string $string;
    private ?string $nullableString;

    public function __construct(string $string, ?string $nullableString)
    {
        $this->string = $string;
        $this->nullableString = $nullableString;
    }
}
