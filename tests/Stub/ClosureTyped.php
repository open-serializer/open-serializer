<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

use Closure;

final class ClosureTyped
{
    private Closure $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }
}
