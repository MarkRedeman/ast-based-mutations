<?php

declare(strict_types=1);

namespace Renamed\Mutations\ConditionalNegation;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Replace (!=) with (==)
 */
final class NotEqual implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\NotEqual) {
            return;
        }

        yield new BinaryOp\Equal($node->left, $node->right, $node->getAttributes());
    }
}
