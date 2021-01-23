<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class DefaultsTyped
{
    private string $string = 'a string';
    private ?string $nullableString = null;

    public function __construct(?string $string = null, ?string $nullableString = null)
    {
        if ($string !== null) {
            $this->string = $string;
        }

        if ($nullableString !== null) {
            $this->nullableString = $nullableString;
        }
    }
}
