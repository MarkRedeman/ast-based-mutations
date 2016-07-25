<?php

declare(strict_types=1);

namespace Renamed;

use PhpParser\Node;

final class Mutation
{
    /**
     * @var Node
     */
    private $original;

    /**
     * Ideally a mutation should be a Node, but we also want
     * to support removing and duplicating nodes
     * @var Node|array|\PhpParser\NodeTraverser\NodeTraverser::REMOVE_NODE
     */
    private $mutation;

    public function __construct(Node $original, $mutation)
    {
        $this->original = $original;
        $this->mutation = $mutation;
    }

    /**
     * @return Node reference to the original node
     */
    public function original() : Node
    {
        return $this->original;
    }

    /**
     * @return Node|array|\PhpParser\NodeTraverser\NodeTraverser::REMOVE_NODE
     */
    public function mutation() //: Node
    {
        return $this->mutation;
    }
}
