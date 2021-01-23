<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class BooleansTyped
{
    private bool $boolean;
    private ?bool $nullableBoolean;

    public function __construct(bool $boolean, ?bool $nullableBoolean)
    {
        $this->boolean         = $boolean;
        $this->nullableBoolean = $nullableBoolean;
    }
}
