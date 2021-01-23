<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class BooleansDocs
{
    /** @var bool */
    private $boolean;
    /** @var null|bool */
    private $nullableBoolean;

    public function __construct(bool $boolean, ?bool $nullableBoolean)
    {
        $this->boolean = $boolean;
        $this->nullableBoolean = $nullableBoolean;
    }
}
