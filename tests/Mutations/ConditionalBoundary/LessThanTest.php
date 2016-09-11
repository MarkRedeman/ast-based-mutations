<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\ConditionalBounary;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalBoundary\LessThan;
use Renamed\MutationOperator;

class LessThanTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LessThan;
    }

    /** @test */
    function it_mutates_less_than_to_less_than_or_equal()
    {
        $this->mutates("4 < 3;")->to("4 <= 3;");
    }

    /** @test */
    function it_only_mutates_less_than_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
