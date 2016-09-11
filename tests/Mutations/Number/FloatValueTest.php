<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Number\FloatValue;
use Renamed\MutationOperator;

class FloatValueTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new FloatValue;
    }

    /** @test */
    function it_mutates_float_values()
    {
        $this->mutates('0.0;')->to('1.0;');
        $this->mutates('1.0;')->to('0.0;');
        $this->mutates('1.3;')->to('2.3;');
        $this->mutates('3.141;')->to('1.0;');
    }

    /** @test */
    function it_only_mutates_float_values()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
