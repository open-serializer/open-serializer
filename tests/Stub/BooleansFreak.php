<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class BooleansFreak
{
    /** @var true */
    private $true;
    /** @var false */
    private $false;

    /**
     * @param true $true
     * @param false $false
     */
    public function __construct(bool $true, bool $false)
    {
        $this->true = $true;
        $this->false = $false;
    }
}
