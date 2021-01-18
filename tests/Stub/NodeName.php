<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class NodeName
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
