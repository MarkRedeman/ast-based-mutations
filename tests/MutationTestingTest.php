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
    /** @test */
    function it_creates_mutations_based_on_an_abstract_syntax_tree()
    {
        $code = "<?php echo 1 * 2 * 3;";
        $ast = $this->generateASTFromCode($code);
        $mutations = $this->generateMutations($ast);

        // Keep track of the mutated source code by adding the pretty printed
        // ASTs to the results array
        $results = [];
        $apply = new ApplyMutation($ast);

        foreach ($mutations as $mutation) {
            $apply->apply($mutation, function ($mutation, $ast) use (&$results) {
                $results[] = (new Standard)->prettyPrint($ast);
            });
        }

        $this->assertEquals([
            'echo 1 / 2 * 3;',
            'echo 1 * 2 / 3;'
        ], $results);
    }

    private function generateASTFromCode(string $code) : array
    {
        $lexer = new Lexer(['usedAttributes' => ['startline', 'endline']]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        return $parser->parse($code);
    }

    private function generateMutations(array $ast) : array
    {
        // Each node in the AST will be passed to the generator, which generates
        // a set of mutations for the given AST
        $mutations = [];
        $generator = new GenerateMutations(
            function (Mutation $mutation) use (&$mutations) {
                $mutations[] = $mutation;
            },
            new Multiplication
        );
        $generator->generate($ast);

        // The Multiplication operator generates 1 mutation per BinaryOp\Mul node
        $this->assertCount(2, $mutations);

        return $mutations;
    }
}
