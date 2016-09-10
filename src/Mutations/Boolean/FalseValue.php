<?php

declare(strict_types=1);

namespace Renamed\Mutations\Boolean;

use PhpParser\Node;
use Renamed\MutationOperator;

/**
 * Changes true to false
 */
final class FalseValue implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof Node\Name) {
            return;
        }

        if ($node->parts !== ["true"]) {
            return;
        }

        yield new Node\Name(
            ['false'],
            $node->getAttributes()
        );
    }
}
