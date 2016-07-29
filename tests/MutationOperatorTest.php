<?php

declare(strict_types=1);

namespace Renamed\Tests;

use Generator;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node;
use PHPUnit_Framework_TestCase as TestCase;
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
    private $code;

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

    protected function doesNotMutate(string $code)
    {
        $operator = $this->operator();
        $original = $this->asAst($code);
        $mutation = $operator->mutate($original[0]);

        $this->assertNull($mutation->current());
    }

    protected function mutates(string $code)
    {
        $this->code = $code;
        return $this;
    }

    protected function to(string $expected)
    {
        $expected = $this->asCode($this->asAst($expected));
        $operator = $this->operator();

        $original = $this->asAst($this->code);

        $mutations = [];
        foreach ($operator->mutate($original[0]) as $mutation) {
            $mutations[] = $mutation;
        }

        $this->assertNotEmpty($mutations, "Operator did not produce any mutaitons");

        $pretty = array_map(function ($mutation) {
            return $this->asCode($mutation);
        }, $mutations);

        $this->assertContains($expected, $pretty, "`{$expected}` is not one of the following mutations: \n" . implode("\n", $pretty));
    }

    private function asAst($code)
    {
        $lexer = new Lexer(['usedAttributes' => []]);
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
