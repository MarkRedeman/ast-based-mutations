<?php

declare(strict_types=1);

namespace Renamed\Mutations\Arithmetic;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

/**
 * Replace plus sign (*) with minus sign (/)
 */
final class ShiftRight implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\ShiftRight) {
            return;
        }

        yield new BinaryOp\ShiftLeft($node->left, $node->right, $node->getAttributes());
    }
}
