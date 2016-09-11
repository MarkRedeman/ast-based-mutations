<?php

declare(strict_types=1);

namespace Renamed\Mutations\Number;

use PhpParser\Node;
use PhpParser\Node\Scalar\LNumber;
use Renamed\MutationOperator;

/**
 * Replace 1 with 0, 0 with 1, or increment.
 */
final class IntegerValue implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof LNumber) {
            return;
        }

        $attributes = $node->getAttributes();
        $value = $node->value;

        if ($value === 1) {
            yield new LNumber(0, $attributes);
        }

        yield new LNumber($value + 1, $attributes);
    }
}
