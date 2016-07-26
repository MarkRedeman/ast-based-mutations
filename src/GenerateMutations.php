<?php

declare(strict_types=1);

namespace Renamed;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Renamed\MutationOperator;
use Renamed\Mutation;

final class GenerateMutations extends NodeVisitorAbstract
{
    private $mutations = [];
    private $operators = [];

    public function __construct(MutationOperator ...$operators)
    {
        $this->operators = $operators;
    }

    /**
     * Generate a set of mutations for the given AST
     * @param array $ast
     * @param array Mutation[]
     */
    public function generate(array $ast) : array
    {
        // Next we pass the generator to a NodeTraverser so that it is called
        // for each node in the AST
        $traverser = new NodeTraverser;
        $traverser->addVisitor($this);
        $traverser->traverse($ast);

        // Get rid of circular dependency
        $traverser->removeVisitor($this);

        // Next we can collect the mutations from the visitor
        return $this->mutations;
    }

    /**
     * Generate a mutation when leaving a node in the AST
     * @param Node $node AST node
     */
    public function leaveNode(Node $node) {
        foreach ($this->operators as $operator) {
            foreach ($operator->mutate($node) as $mutation) {
                // Though we currently keep all mutations in memory (no
                // taking advantage of the generators), in the future
                // we could instead immediately test the given mutaiton
                // by firing an event
                $this->mutations[] = new Mutation($node, $mutation);
            }
        }
    }
};
