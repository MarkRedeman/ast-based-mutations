<?php

declare(strict_types=1);

namespace Renamed\Mutations\Boolean;

use PhpParser\Node;
use Renamed\MutationOperator;

/**
 * Changes false to true
 */
final class TrueValue implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof Node\Name) {
            return;
        }

        if ($node->parts !== ["false"]) {
            return;
        }

        yield new Node\Name(
            ['true'],
            $node->getAttributes()
        );
    }
}
