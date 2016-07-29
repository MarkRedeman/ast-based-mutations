<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations;

use Renamed\MutationOperator;
use Renamed\Mutations\ReturnNull;
use Renamed\Tests\MutationOperatorTest as TestCase;

class ReturnNullTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new ReturnNull;
    }

    /** @test */
    function it_replaces_a_return_statement()
    {
        $this->mutates('return statement();')->to('statement(); return;');
        $this->mutates('return new statement();')->to('new statement(); return;');
        $this->mutates('return $this;')->to('$this; return;');
        $this->mutates('return (1 + 2 + 3);')->to('(1 + 2 + 3); return;');
    }

    /** @test */
    function it_only_mutates_return_statements()
    {
        $this->doesNotMutate('new StdClass;');
        $this->doesNotMutate('$hello = "world";');
    }
}
