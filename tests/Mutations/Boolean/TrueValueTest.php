<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\Boolean;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Boolean\TrueValue;
use Renamed\MutationOperator;

class TrueValueTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new TrueValue;
    }

    /** @test */
    function it_mutates_false_to_true()
    {
        $this->mutates("false;")->to("true;");
    }

    /** @test */
    function it_does_not_mutate_nodes_are_not_false()
    {
        $this->doesNotMutate('$hello = "world";');
        $this->doesNotMutate('true;');
    }
}
