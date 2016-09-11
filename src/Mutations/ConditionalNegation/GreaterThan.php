<?php

declare(strict_types=1);

namespace Renamed\Mutations\ConditionalNegation;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Replace (>) with (<=)
 */
final class GreaterThan implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\Greater) {
            return;
        }

        yield new BinaryOp\SmallerOrEqual($node->left, $node->right, $node->getAttributes());
    }
}
