<?php

declare(strict_types=1);

namespace Renamed\Mutations\Arithmetic;

use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp;
use Renamed\MutationOperator;

/**
 * Replace plus sign (%=) with minus sign (*=)
 */
final class ModEqual implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof AssignOp\Mod) {
            return;
        }

        yield new AssignOp\Mul($node->var, $node->expr, $node->getAttributes());
    }
}
