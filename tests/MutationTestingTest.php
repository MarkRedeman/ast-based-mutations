<?php

declare(strict_types=1);

namespace Renamed\Tests;

use Closure;
use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Renamed\ApplyMutation;
use Renamed\GenerateMutations;
use Renamed\Mutation;
use Renamed\MutationOperator;
use Renamed\Mutations\Multiplication;

class MutationTestingTest extends TestCase
{
    /** @test */
    function it_creates_mutations_based_on_an_abstract_syntax_tree()
    {
        $code = "<?php echo 1 * 2 * 3;";
        $ast = $this->generateASTFromCode($code);
        $mutations = $this->generateMutations($ast);

        echo "\n";

        foreach ($mutations as $mutation) {
            $this->applyMutation($ast, $mutation, function($code) {
                $prettyPrinter = new Standard;
                echo $prettyPrinter->prettyPrint($code) . "\n";
            });
        }
    }

    private function generateASTFromCode(string $code) : array
    {
        $lexer = new Lexer(['usedAttributes' => ['startline', 'endline']]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        return $parser->parse($code);
    }

    private function generateMutations($ast) : array
    {
        // Each node in the AST will be passed to the generator, which generates
        // a set of mutations for the given AST
        $generator = new GenerateMutations(new Multiplication);

        // Next we pass the generator to a NodeTraverser so that it is called
        // for each node in the AST
        $traverser = new NodeTraverser;
        $traverser->addVisitor($generator);
        $traverser->traverse($ast);

        // Next we can collect the mutations from the visitor
        $mutations = $generator->mutations();

        // The Multiplication operator generates 1 mutation per BinaryOp\Mul node
        $this->assertCount(2, $mutations);

        return $mutations;
    }

    private function applyMutation($ast, Mutation $mutation, Closure $action)
    {
        $apply = new ApplyMutation($mutation);

        echo "By traversal:\n";
        $this->applyMutationByTraversals($ast, $apply, $action);
        echo "\n";
        echo "By copy:\n";
        $this->applyMutationByCopy($ast, $apply, $action);
        echo "\n";
    }

    private function applyMutationByTraversals($ast, ApplyMutation $apply, Closure $action)
    {
        $traverser = new NodeTraverser;
        $traverser->addVisitor($apply);

        // First apply the mutation, then take action and revert the mutation
        $traverser->traverse($ast);
        $action($ast);
        $traverser->traverse($ast);
    }

    private function applyMutationByCopy($ast, ApplyMutation $apply, Closure $action)
    {
        // While traversing the ast, copy each node
        $traverser = new NodeTraverser(true);
        $traverser->addVisitor($apply);

        $action($traverser->traverse($ast));
    }
}
