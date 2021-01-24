<?php declare(strict_types=1);

namespace OpenSerializer\Examples;

final class Foo
{
    private string $bar;

    public function __construct(string $bar)
    {
        $this->bar = $bar;
    }

    public function bar(): string
    {
        return $this->bar;
    }
}
