<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class ResourceDocs
{
    /** @var resource */
    private $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }
}
