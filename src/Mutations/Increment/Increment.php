<?php

declare(strict_types=1);

namespace Renamed\Mutations\Increment;

use PhpParser\Node;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use Renamed\MutationOperator;

/**
 * Replace (++) with (--)
 */
final class Increment implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if ($node instanceof PostInc) {
            yield new PostDec($node->var, $node->getAttributes());
        }

        if ($node instanceof PreInc) {
            yield new PreDec($node->var, $node->getAttributes());
        }
    }
}
