<?php

declare(strict_types=1);

namespace Renamed;

use Closure;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Renamed\Mutation;

final class ApplyMutation extends NodeVisitorAbstract
{
    private $ast;

    /**
     * @param array $ast The given AST on which mutations should be applied
     */
    public function __construct(array $ast)
    {
        $this->ast = $ast;
    }

    /**
     * Applies the given mutation on the ast and performs an action
     * @param array $ast
     * @param Closure $action will be called with the ast and mutation as
     * arguments after applying the mutation
     * @param bool $copy if true the ast will be copied when applying a
     * mutation
     */
    public function apply(Mutation $mutation, Closure $action)
    {
        $traverser = new NodeTraverser;
        $traverser->addVisitor($this->visitor($mutation));

        // First apply the mutation, then take action and revert the mutation
        $this->ast = $traverser->traverse($this->ast);
        $action($mutation, $this->ast);
        $this->ast = $traverser->traverse($this->ast);
    }

    /**
     * Returns an anonymous class instance taht acts as a Node visitor
     * this visitor will mutate the ast based on the given mutation.
     */
    private function visitor(Mutation $mutation) : NodeVisitor
    {
        return new class($mutation) extends NodeVisitorAbstract
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
    }
};
