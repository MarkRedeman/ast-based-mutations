<?php

declare(strict_types=1);

namespace Renamed\Experiments;

use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Expr\BinaryOp;

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
                $prettyPrinter = new \PhpParser\PrettyPrinter\Standard;
                echo $prettyPrinter->prettyPrint($code) . "\n";
            });
        }
    }

    private function generateASTFromCode(string $code) : array
    {
        // First we will need to find the AST representation of $code
        $lexer = new \PhpParser\Lexer([
            'usedAttributes' => ['startline', 'endline']
        ]);

        $parser = (new \PhpParser\ParserFactory)->create(\PhpParser\ParserFactory::PREFER_PHP7, $lexer);

        $ast = $parser->parse($code);
        $this->assertInstanceOf(Node::class, $ast[0]);

        return $ast;
    }

    private function generateMutations($ast) : array
    {
        // Next we want to generate mutations, we do this by traversing the AST
        $traverser = new NodeTraverser;

        // We will need a visitor that tries to generate mutations for each AST node
        // this visitor will keep track of all of the generated mutations
        $visitor = new class extends NodeVisitorAbstract {
                private $mutations = [];
                private $operators = [];

                public function __construct()
                {
                    $this->operators = [new Multiplication];
                }

                public function leaveNode(Node $node) {
                    foreach ($this->operators as $operator) {
                        foreach ($operator->mutate($node) as $mutation) {
                            // Though we currently keep all mutations in memory (no
                            // taking advantage of the generators), in the future
                            // we could instead immediately test the given mutaiton
                            // by firing an event
                            $this->mutations[] = new Mutation($node, $mutation);
                        }
                    }
                }

                public function mutations() : array
                {
                    return $this->mutations;
                }
            };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        // Next we can collect the mutations from the visitor
        $mutations = $visitor->mutations();

        // AOR creates 26 mutations for each binary operator
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

interface MutationOperator {

    public function mutate(Node $node);
}

final class Multiplication implements MutationOperator
{
    /**
     * Replace (*) with (/)
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof BinaryOp\Mul) {
            return;
        }

        yield new BinaryOp\Div($node->left, $node->right, $node->getAttributes());
    }
}

final class Mutation
{
    /**
     * @var Node
     */
    private $original;

    /**
     * Ideally a mutation should be a Node, but we also want
     * to support removing and duplicating nodes
     * @var Node|array|\PhpParser\NodeTraverser\NodeTraverser::REMOVE_NODE
     */
    private $mutation;

    public function __construct(Node $original, $mutation)
    {
        $this->original = $original;
        $this->mutation = $mutation;
    }

    /**
     * @return Node reference to the original node
     */
    public function original() : Node
    {
        return $this->original;
    }

    /**
     * @return Node|array|\PhpParser\NodeTraverser\NodeTraverser::REMOVE_NODE
     */
    public function mutation() //: Node
    {
        return $this->mutation;
    }

    /**
     * The filename and linenumber which this mutation acts on can be
     * used to determine the tests to be run for this mutation.
     * Though I don' really like this idea because it is only used for
     * improving performance. Maybe there is a better way to do this..
     */
    public function linenumber() : int
    {
        throw \Exception;
    }
}
