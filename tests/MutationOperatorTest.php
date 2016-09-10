<?php

declare(strict_types=1);

namespace Renamed\Tests;

use Closure;
use Generator;
use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Renamed\MutateSourceCode;
use Renamed\MutationOperator;

/**
 * This helper test class may be used to easily test a mutation operator
 * Currently it only supports testing 1 node, later I would like to add
 * options to check that multiple nodes can be mutated
 *
 * For this we will need well written ApplyMutation and
 * Generate Mutations instances
 */
abstract class MutationOperatorTest extends TestCase
{
    /**
     * @var string used to provide fluent syntax
     */
    private $code;

    /**
     * @var bool used to check if an expected mutation was found
     */
    private $found = false;

    abstract protected function operator() : MutationOperator;

    /** @test */
    function it_returns_a_generator()
    {
        $node = $this->prophesize(Node::class);
        $operator = $this->operator();

        $this->assertInstanceOf(
            Generator::class,
            $operator->mutate($node->reveal())
        );
    }

    // The following are helper functions that make it easier to test the output
    // of a given mutation operator
    // Example:
    // $this->mutates('3 + 3;')->to('3 - 3;');

    protected function mutates(string $code)
    {
        $this->code = $code;
        return $this;
    }

    protected function to(string $expected)
    {
        $source = '<?php ' . $this->code;
        $mutate = new MutateSourceCode($this->operator());
        $mutate->mutate(
            $source,
            $this->findMutation($this->asAst($expected))
        );

        $this->assertTrue($this->found, "The operator did not produce the expected mutation");
    }

    protected function doesNotMutate(string $code)
    {
        $source = '<?php ' . $code;
        $mutate = new MutateSourceCode($this->operator());
        $mutate->mutate(
            $source,
            function () {
                $this->assertFalse(true, "Did not expect to find a mutation");
            }
        );
    }

    /**
     * Saves the pretty printed mutated AST into the $results property
     */
    private function findMutation(array $expected) : Closure
    {
        $this->found = false;

        return function ($mutation, $ast) use ($expected) {
            // First we will revert both the expected and the generated AST
            // to text. This is because when we would check the equality
            // of $ast and $expected the test will fail when the attributes
            // (startLine, endLine etc) of a node have been changed.
            if ($this->asCode($ast) == $this->asCode($expected)) {
                $this->found = true;
            }
        };
    }

    private function asAst($code)
    {
        $lexer = new Lexer(['usedAttributes' => ['startLine', 'endLine']]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        $ast = $parser->parse("<?php " . $code);
        return $ast;
    }

    private function asCode($ast)
    {
        $printer = new Standard;

        if (is_array($ast)) {
            return $printer->prettyPrint($ast);
        }
        return $printer->prettyPrint([$ast]);
    }
}
