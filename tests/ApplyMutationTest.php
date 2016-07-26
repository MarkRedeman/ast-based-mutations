<?php

declare(strict_types=1);

namespace Renamed\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Node\Scalar\LNumber;
use Renamed\ApplyMutation;
use Renamed\Mutation;

class ApplyMutationTest extends TestCase
{
    /** @test */
    function it_performs_an_action_after_a_mutation_has_been_applied()
    {
        $applied = false;
        $original = LNumber::fromString("1");
        $target = LNumber::fromString("2");

        $ast = [$original];
        $mutation = new Mutation($original, $target);

        $apply = new ApplyMutation($ast);
        $apply->apply($mutation, function (Mutation $mutation, $ast) use (&$applied, $target) {
            // Check that the AST has successfully been mutated
            $this->assertEquals([$target], $ast);
            $applied = true;
        });

        // Check that the action has been performed
        $this->assertTrue($applied);
    }
}
