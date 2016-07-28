<?php

declare(strict_types=1);

namespace Renamed\Tests;

use Closure;
use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\PrettyPrinter\Standard;
use Renamed\Mutations\Multiplication;
use Renamed\MutateSourceCode;

class MutationSourceCodeTest extends TestCase
{
    /**
     * Stores the applied mutations
     * @var array
     */
    private $results = [];

    /**
     * This test shows how the process of generating, applying and storing
     * mutations works.
     * @test
     */
    function it_creates_mutations_based_on_an_abstract_syntax_tree()
    {
        $source = "<?php echo 1 * 2 * 3;";

        $mutate = new MutateSourceCode(
            new Multiplication
        );

        $mutate->mutate($source, $this->storeAppliedMutations());

        $this->assertEquals([
            'echo 1 / 2 * 3;',
            'echo 1 * 2 / 3;'
        ], $this->results);
    }

    /** @test */
    function it_should_not_swap_actual_code_with_mutations()
    {
        $source = "<?php echo 2 / 2 + 2 * 2;";

        $mutate = new MutateSourceCode(
            new Multiplication
        );

        $mutate->mutate($source, $this->storeAppliedMutations());

        $this->assertEquals([
            'echo 2 / 2 + 2 / 2;',
        ], $this->results);
    }

    /**
     * Saves the pretty printed mutated AST into the $results property
     */
    private function storeAppliedMutations() : Closure
    {
        return function ($mutation, $ast) {
            $this->results[] = (new Standard)->prettyPrint($ast);
        };
    }

    /**
     * When called with a mutation and AST, it will print
     * the prettyPrinted code
     */
    private function printAppliedMutations() : Closure
    {
        return function ($mutation, $ast) {
            echo (new Standard)->prettyPrint($ast);
        };
    }
}
