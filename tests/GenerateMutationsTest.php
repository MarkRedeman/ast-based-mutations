<?php

declare(strict_types=1);

namespace Renamed\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PhpParser\Node;
use Renamed\GenerateMutations;
use Renamed\MutationOperator;
use Renamed\Mutation;

class GenerateMutationsTest extends TestCase
{
    private $ast;

    /**
     * @before
     */
    public function setupDefaultAST()
    {
        $this->ast = $this->generateASTFromCode('<?php 1 + 2 + 3;');
    }

    /** @test */
    function it_cant_create_mutatins_if_no_mutation_operators_have_been_applied()
    {
        $mutations = [];
        $generator = new GenerateMutations($this->afterGeneration($mutations));
        $generator->generate($this->ast);

        $this->assertEmpty($mutations);
    }

    /** @test */
    function it_generates_mutations_from_a_given_AST()
    {
        // Use a null operator, that it it changes each node to `null`
        $operator = new class implements MutationOperator {
                public function mutate(Node $node)
                {
                    yield null;
                }
            };

        $mutations = [];
        $generator = new GenerateMutations($this->afterGeneration($mutations));
        $generator->generate($this->ast);

        // Check if the types are correct
        $this->assertContainsOnlyInstancesOf(Mutation::class, $mutations);

        // Now check that each node will be mutated
        foreach ($mutations as $mutation) {
            $this->assertNull($mutation->mutation());
        }
    }

    private function generateASTFromCode(string $code) : array
    {
        $lexer = new Lexer(['usedAttributes' => ['startline', 'endline']]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        return $parser->parse($code);
    }

    /**
     * Returns a function that will be called after a mutation has been
     * generated.
     * We pass an array of mutations by reference so that we can find
     * the mutations that have been generated.
     */
    private function afterGeneration(&$mutaitons)
    {
        return function (Mutation $mutation) use (&$mutations) {
            $mutations[] = $mutation;
        };
    }
}
