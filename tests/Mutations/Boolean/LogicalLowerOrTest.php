<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\Boolean;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Boolean\LogicalLowerOr;
use Renamed\MutationOperator;

class LogicalLowerOrTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LogicalLowerOr;
    }

    /** @test */
    function it_mutates_or_to_and()
    {
        $this->mutates("if (true or true);")->to("if (true and true);");
    }

    /** @test */
    function it_only_mutates_boolean_and_operators()
    {
        $this->doesNotMutate('$hello = "world";');
        $this->doesNotMutate('true || true;');
    }
}
