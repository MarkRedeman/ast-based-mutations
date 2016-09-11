<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\ConditionalBounary;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalBoundary\LessThanOrEqual;
use Renamed\MutationOperator;

class LessThanOrEqualTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LessThanOrEqual;
    }

    /** @test */
    function it_mutates_less_than_or_equal_to_less_than()
    {
        $this->mutates("4 <= 3;")->to("4 < 3;");
    }

    /** @test */
    function it_only_mutates_less_than_or_equal_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
