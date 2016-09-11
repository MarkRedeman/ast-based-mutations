<?php

declare(strict_types=1);

namespace Renamed\Mutations\Arithmetic;

use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp;
use Renamed\MutationOperator;

/**
 * Replace plus sign (+=) with minus sign (-=)
 */
final class PlusEqual implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof AssignOp\Plus) {
            return;
        }

        yield new AssignOp\Minus($node->var, $node->expr, $node->getAttributes());
    }
}
