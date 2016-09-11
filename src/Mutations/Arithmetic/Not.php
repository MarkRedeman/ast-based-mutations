<?php

declare(strict_types=1);

namespace Renamed\Mutations\Arithmetic;

use PhpParser\Node;
use PhpParser\Node\Expr\BitwiseNot;
use Renamed\MutationOperator;

/**
 * Replace plus sign (*) with minus sign (/)
 */
final class Not implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BitwiseNot) {
            return;
        }

        yield $node->expr;
    }
}
