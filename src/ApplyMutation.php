<?php

declare(strict_types=1);

namespace Renamed;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverserInterface;
use Renamed\Mutation;

final class ApplyMutation extends NodeVisitorAbstract
{
    private $mutation;

    public function __construct(Mutation $mutation)
    {
        $this->mutation = $mutation;
    }

    /**
     * Either apply the mutation, or revert the mutation
     */
    public function leaveNode(Node $node)
    {
        // If we visit the original node, then replace it with the mutated node
        if ($node == $this->mutation->original()) {
            return $this->mutation->mutation();
        }

        // If we visit the mutated node, then replace it with the original node
        if ($node == $this->mutation->mutation()) {
            return $this->mutation->original();
        }
    }

    /**
     * Once we've applied or reverted the mutation, we don't want to keep
     * traversing the AST.
     */
    public function enterNode(Node $node)
    {
        if ($node == $this->mutation->original() || $node == $this->mutation->mutation()) {
            return NodeTraverserInterface::DONT_TRAVERSE_CHILDREN;
        }
    }

};
