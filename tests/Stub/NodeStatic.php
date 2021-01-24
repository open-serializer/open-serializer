<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

class NodeStatic
{
    private string $name;
    /** @var static[] */
    private array $children;

    /**
     * @param static ...$children
     */
    public function __construct(string $name, ...$children)
    {
        $this->name = $name;
        $this->children = $children;
    }
}
