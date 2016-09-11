<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\ConditionalBounary;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalBoundary\GreaterThanOrEqual;
use Renamed\MutationOperator;

class GreaterThanOrEqualTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new GreaterThanOrEqual;
    }

    /** @test */
    function it_mutates_greater_than_or_equal_to_greater_than()
    {
        $this->mutates("4 >= 3;")->to("4 > 3;");
    }

    /** @test */
    function it_only_mutates_greater_than_or_equal_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
