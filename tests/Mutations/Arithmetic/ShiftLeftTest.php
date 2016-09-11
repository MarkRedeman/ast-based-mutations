<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\Arithmetic;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Arithmetic\ShiftLeft;
use Renamed\MutationOperator;

class ShiftLeftTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new ShiftLeft;
    }

    /** @test */
    function it_mutates_source_code()
    {
        $this->mutates('32 << 5;')->to('32 >> 5;');
    }

    /** @test */
    function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
