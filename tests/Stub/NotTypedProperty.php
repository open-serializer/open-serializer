<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class NotTypedProperty
{
    /**
     * Some prop is this!
     * @deprecated
     */
    private $prop;

    public function __construct($prop)
    {
        $this->prop = $prop;
    }
}
