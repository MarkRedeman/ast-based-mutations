<?php

declare(strict_types=1);

namespace Renamed;

use Closure;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeVisitor;
use Renamed\MutationOperator;
use Renamed\Mutation;

final class GenerateMutations
{
    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @param Closure(Mutation $mutation) $generated will be called after a
     * Mutation has been generated
     */
    public function __construct(Closure $generated, MutationOperator ...$operators)
    {
        // Next we pass the generator to a NodeTraverser so that it is called
        // for each node in the AST
        $this->traverser = new NodeTraverser;
        $this->traverser->addVisitor(
            $this->visitor($generated, ...$operators)
        );
    }

    /**
     * Generate a set of mutations for the given AST
     * @param array $ast
     * @param array Mutation[]
     */
    public function generate(array $ast)
    {
        $this->traverser->traverse($ast);
    }

    /**
     * Returns an anonymous class instance that acts as a Node visitor
     * this visitor will create mutations using its mutation operators
     */
    private function visitor(Closure $generated, MutationOperator ...$operators) : NodeVisitor
    {
        return new class($generated, ...$operators) extends NodeVisitorAbstract {

            private $generated;
            private $operators;

            public function __construct(Closure $generated, MutationOperator ...$operators)
            {
                $this->generated = $generated;
                $this->operators = $operators;
            }

            /**
             * Generate a mutation when leaving a node in the AST
             * @param Node $node AST node
             */
            public function leaveNode(Node $node) {
                foreach ($this->operators as $operator) {
                    foreach ($operator->mutate($node) as $mutation) {
                        ($this->generated)(new Mutation($node, $mutation));
                    }
                }
            }

        };
    }
};
