<?php

declare(strict_types=1);

namespace Renamed\Mutations;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use Renamed\MutationOperator;

final class BinaryOperatorReplacement implements MutationOperator
{
    private $replacements = [
        BinaryOp\BitwiseAnd::class,
        BinaryOp\BitwiseOr::class,
        BinaryOp\BitwiseXor::class,
        BinaryOp\BooleanAnd::class,
        BinaryOp\BooleanOr::class,
        BinaryOp\Coalesce::class,
        BinaryOp\Concat::class,
        BinaryOp\Div::class,
        BinaryOp\Equal::class,
        BinaryOp\Greater::class,
        BinaryOp\GreaterOrEqual::class,
        BinaryOp\Identical::class,
        BinaryOp\LogicalAnd::class,
        BinaryOp\LogicalOr::class,
        BinaryOp\LogicalXor::class,
        BinaryOp\Minus::class,
        BinaryOp\Mod::class,
        BinaryOp\Mul::class,
        BinaryOp\NotEqual::class,
        BinaryOp\NotIdentical::class,
        BinaryOp\Plus::class,
        BinaryOp\Pow::class,
        BinaryOp\ShiftLeft::class,
        BinaryOp\ShiftRight::class,
        BinaryOp\Smaller::class,
        BinaryOp\SmallerOrEqual::class,
        BinaryOp\Spaceship::class
    ];

    /**
     * Replace a binary operator expression with a different operator
     * If applicable this operator will always generate 26 mutations
     * 26 == (count($this->expressions) - 1)
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp) {
            return;
        }

        // Replace the node with a different binary operator
        $name = get_class($node);

        // Don't include the original binary operator in the mutations
        $operators = array_filter(
            $this->replacements,
            function($operator) use ($name) {
                return $name !== $operator;
            }
        );

        // We prefer to use a yield instead of mapping the operators
        // into mutations, so that we can leave the return value of
        // this function as void and have an op
        foreach ($operators as $operator) {
            yield new $operator($node->left, $node->right, $node->getAttributes());
        }
    }
}
