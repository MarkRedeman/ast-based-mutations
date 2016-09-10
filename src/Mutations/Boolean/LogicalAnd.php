<?php

declare(strict_types=1);

namespace Renamed\Mutations\Boolean;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Changes && to ||
 */
final class LogicalAnd implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\BooleanAnd) {
            return;
        }

        yield new BinaryOp\BooleanOr($node->left, $node->right, $node->getAttributes());
    }
}
