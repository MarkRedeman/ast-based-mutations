<?php

declare(strict_types=1);

namespace Renamed\Experiments;

use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
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

    public function applyMutation($ast, Mutation $mutation, \Closure $action)
    {
        // To apply the mutation we will use a trick that I saw at
        // http://schinckel.net/2015/10/15/thoughts-on-mutation-testing-in-python/
        // We will traverse the ast two times to do this, the first
        // time we traverse the node we will apply the mutation,
        // the second time we will reverse the mutation so that
        // we can continue applying a second mutation
        $traverser = new NodeTraverser(false);
        $visitor = new class($mutation) extends NodeVisitorAbstract
        {
            private $mutation;

            public function __construct(Mutation $mutation)
            {
                $this->mutation = $mutation;
            }

            public function leaveNode(Node $node)
            {
                if ($node == $this->mutation->original())
                {
                    return $this->mutation->mutation();
                }

                if ($node == $this->mutation->mutation())
                {
                    return $this->mutation->original();
                }
            }
        };

        $traverser->addVisitor($visitor);

        // We can choose to either copy the ast which might use more memory
        // or we can traverse the ast two times

        // Assuming the nodes have been cloned, we may
        // perform the action on the returned ast
        // $action($traverser->traverse($ast));

        // Instead we may also traverse the ast twice, once applying
        // and once reverting
        $traverser->traverse($ast); // apply
        $action($ast);
        $traverser->traverse($ast); // revert
    }

}
