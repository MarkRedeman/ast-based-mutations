<?php

declare(strict_types=1);

namespace Renamed\Mutations\Arithmetic;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Replace plus sign (/) with minus sign (*)
 */
final class Division implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\Div) {
            return;
        }

        yield new BinaryOp\Mul($node->left, $node->right, $node->getAttributes());
    }
}
