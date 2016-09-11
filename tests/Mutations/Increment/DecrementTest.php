<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Increment\Decrement;
use Renamed\MutationOperator;

class DecrementTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new Decrement;
    }

    /** @test */
    function it_mutates_post_increment_operator_to_post_decrement()
    {
        $this->mutates('$hello--;')->to('$hello++;');
    }

    /** @test */
    function it_mutates_pre_increment_operator_to_pre_decrement()
    {
        $this->mutates('--$hello;')->to('++$hello;');
    }

    /** @test */
    function it_only_mutates_equal_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
