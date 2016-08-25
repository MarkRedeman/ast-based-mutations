<?php

declare(strict_types=1);

namespace Renamed\Mutations;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use Renamed\MutationOperator;

final class DateTimeFromFormat implements MutationOperator
{
    /**
     * Replaces
     * -new DateTime($date, $timezone = null)
     * +DateTime::createFromFormat(DateTime::ISO8601, $date, $timezone = null)
     *
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof New_) {
            return;
        }

        // check that we are a class name
        if (! $node->class instanceof Name) {
            return;
        }

        if ($node->class->toString() !== "DateTime") {
            return;
        }

        yield $this->createFromFormat("ISO8601", $node->args);
    }

    /**
     * @param string $format the format used by the constructor
     * @param array $args the arguments used by the original constructor
     * @return Staticcall
     */
    private function createFromFormat(string $format, array $args)
    {
        return new StaticCall(
            new Name("DateTime"),
            "createFromFormat",
            array_merge(
                [$this->format($format)],
                $args
            )
        );
    }

    /**
     * @param string $format the format used by the constructor
     * @return Arg
     */
    private function format(string $format)
    {
        return new Arg(new ClassConstFetch(new Name("DateTime"), $format));
    }
}
