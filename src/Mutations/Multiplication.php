<?php

declare(strict_types=1);

namespace Renamed\Mutations;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

final class Multiplication implements MutationOperator
{
    /**
     * Replace (*) with (/)
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\Mul) {
            return;
        }

        yield new BinaryOp\Div($node->left, $node->right, $node->getAttributes());
    }
}
