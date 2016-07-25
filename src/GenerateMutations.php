<?php

declare(strict_types=1);

namespace Renamed;

use PhpParser\Node;
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

    public function mutations() : array
    {
        return $this->mutations;
    }
};
