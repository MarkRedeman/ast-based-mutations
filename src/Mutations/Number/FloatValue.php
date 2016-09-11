<?php

declare(strict_types=1);

namespace Renamed\Mutations\Number;

use PhpParser\Node;
use PhpParser\Node\Scalar\DNumber;
use Renamed\MutationOperator;

/**
 * Replace 0.0 with 1.0, 1.0 with 0.0, and float between 1 and 2 is incremented
 * by one, and any float greater than 2 is replaced with 1.0.
 */
final class FloatValue implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof DNumber) {
            return;
        }

        $attributes = $node->getAttributes();
        $value = $node->value;

        if ($value === 1.0) {
            yield new DNumber(0.0, $attributes);
        } elseif ($node->value < 2) {
            yield new DNumber($value + 1, $attributes);
        } else {
            yield new DNumber(1.0, $attributes);
        }
    }
}
