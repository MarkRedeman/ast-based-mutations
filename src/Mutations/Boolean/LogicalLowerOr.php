<?php

declare(strict_types=1);

namespace Renamed\Mutations\Boolean;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Changes or to and
 */
final class LogicalLowerOr implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\LogicalOr) {
            return;
        }

        yield new BinaryOp\LogicalAnd($node->left, $node->right, $node->getAttributes());
    }
}
