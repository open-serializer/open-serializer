<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class Node
{
    private NodeName $name;
    /** @var array<Node> */
    private array $children;

    /**
     * @param Node[] $children
     */
    public function __construct(NodeName $name, array $children)
    {
        $this->name = $name;
        $this->children = $children;
    }
}
