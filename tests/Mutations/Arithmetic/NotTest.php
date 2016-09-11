<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\Arithmetic;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Arithmetic\Not;
use Renamed\MutationOperator;

class NotTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new Not;
    }

    /** @test */
    function it_mutates_source_code()
    {
        $this->mutates('~ $var;')->to('$var;');
    }

    /** @test */
    function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
