<?php

declare(strict_types=1);

namespace Renamed\Mutations\Arithmetic;

use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp;
use Renamed\MutationOperator;

/**
 * Replace plus sign (-=) with minus sign (+=)
 */
final class MinusEqual implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof AssignOp\Minus) {
            return;
        }

        yield new AssignOp\Plus($node->var, $node->expr, $node->getAttributes());
    }
}
