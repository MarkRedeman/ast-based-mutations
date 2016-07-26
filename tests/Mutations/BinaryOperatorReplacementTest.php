<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\BinaryOperatorReplacement;
use Renamed\MutationOperator;

/**
 * This mutation is used by mutant, the ruby mutation testing tool.
 * A nice article explaining the usefulness of this mutation can be found here,
 * https://blog.blockscore.com/how-to-write-better-code-using-mutation-testing/
 */
class BinaryOperatorReplacementTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new BinaryOperatorReplacement;
    }

    /** @test */
    function it_mutates_binary_operators()
    {
        $this->mutates("1 * 2;")->to("1 / 2;");
    }

    /** @test */
    function it_does_not_mutate_nodes_that_arent_binary_operators()
    {
        $this->doesNotMutate('$hello = "world";');
    }

}
