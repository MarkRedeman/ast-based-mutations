<?php

declare(strict_types=1);

namespace Renamed\Tests;

use Closure;
use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Renamed\ApplyMutation;
use Renamed\GenerateMutations;
use Renamed\Mutation;
use Renamed\Mutations\Multiplication;

class MutationTestingTest extends TestCase
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
        $ast = $this->generateASTFromCode($source);

        // Setup the mutation generator, which will apply a mutation once it's
        // been generated
        // The ApplyMutation object will apply the mutation and then call the
        // storeAppliedMutations closure so that we can check that the mutations
        // have successfully been applied
        $generator = new GenerateMutations(
            $this->applyMutationAfterGeneration(
                new ApplyMutation($ast),
                $this->storeAppliedMutations()
            ),
            new Multiplication
        );

        // Generate and store the applied mutations
        $generator->generate($ast);

        $this->assertEquals([
            'echo 1 / 2 * 3;',
            'echo 1 * 2 / 3;'
        ], $this->results);
    }

    private function generateASTFromCode(string $code) : array
    {
        $lexer = new Lexer(['usedAttributes' => ['startline', 'endline']]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        return $parser->parse($code);
    }

    // The following are helper functions that return closures

    /**
     * Will apply a given mutation and call the $afterApplying callback once
     * the mutation has been applied.
     * @param ApplyMutation $apply
     * @param Closure $afterApplying
     */
    private function applyMutationAfterGeneration(
        ApplyMutation $apply,
        Closure $afterApplying
    ) : Closure {
        return function (Mutation $mutation) use ($apply, $afterApplying) {
            $apply->apply($mutation, $afterApplying);
        };
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
