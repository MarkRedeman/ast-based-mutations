<?php

declare(strict_types=1);

namespace Renamed\Mutations\Boolean;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Changes and to or
 */
final class LogicalLowerAnd implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\LogicalAnd) {
            return;
        }

        yield new BinaryOp\LogicalOr($node->left, $node->right, $node->getAttributes());
    }
}
