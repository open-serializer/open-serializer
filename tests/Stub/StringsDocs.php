<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class StringsDocs
{
    /** @var string */
    private $string;
    /** @var null|string */
    private $nullableString;

    public function __construct(string $string, ?string $nullableString)
    {
        $this->string = $string;
        $this->nullableString = $nullableString;
    }
}
