<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Number\IntegerValue;
use Renamed\MutationOperator;

class IntegerValueTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new IntegerValue;
    }

    /** @test */
    function it_mutates_integer_values()
    {
        $this->mutates('0;')->to('1;');
        $this->mutates('1;')->to('0;');
        $this->mutates('3;')->to('4;');
    }

    /** @test */
    function it_only_mutates_integer_values()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
