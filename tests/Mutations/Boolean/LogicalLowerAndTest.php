<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\Boolean;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Boolean\LogicalLowerAnd;
use Renamed\MutationOperator;

class LogicalLowerAndTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LogicalLowerAnd;
    }

    /** @test */
    function it_mutates_and_to_or()
    {
        $this->mutates("if (true and true);")->to("if (true or true);");
    }

    /** @test */
    function it_only_mutates_boolean_and_operators()
    {
        $this->doesNotMutate('$hello = "world";');
        $this->doesNotMutate('true && true;');
    }
}
