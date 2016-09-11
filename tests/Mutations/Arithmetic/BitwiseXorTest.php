<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\Arithmetic;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Arithmetic\BitwiseXor;
use Renamed\MutationOperator;

class BitwiseXorTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new BitwiseXor;
    }

    /** @test */
    function it_mutates_source_code()
    {
        $this->mutates("4 ^ 3;")->to("4 & 3;");
    }

    /** @test */
    function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
