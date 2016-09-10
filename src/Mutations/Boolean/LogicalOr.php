<?php

declare(strict_types=1);

namespace Renamed\Mutations\Boolean;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Changes || to &&
 */
final class LogicalOr implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\BooleanOr) {
            return;
        }

        yield new BinaryOp\BooleanAnd($node->left, $node->right, $node->getAttributes());
    }
}
