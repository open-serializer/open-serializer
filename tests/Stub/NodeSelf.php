<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

final class NodeSelf
{
    private string $name;
    private ?self $child;
    /** @var self[] */
    private array $otherChildren;

    public function __construct(string $name, ?self $child, self ...$otherChildren)
    {
        $this->name = $name;
        $this->child = $child;
        $this->otherChildren = $otherChildren;
    }
}
