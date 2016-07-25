<?php

declare(strict_types=1);

namespace Renamed\Tests;

use Renamed\MutationOperator;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Scalar\LNumber;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * This helper test class may be used to easily test a mutation operator
 * Currently it only supports testing 1 node, later I would like to add
 * options to check that multiple nodes can be mutated
 *
 * For this we will need well written ApplyMutation and
 * GenerateMutations instances
 */
abstract class MutationOperatorTest extends TestCase
{
    private $code;

    abstract protected function operator() : MutationOperator;

    protected function mutates(string $code)
    {
        $this->code = $code;
        return $this;
    }

    protected function to(string $expected)
    {
        $operator = $this->operator();

        $original = $this->asAst($this->code);

        $mutation = $operator->mutate($original)->current();

        $this->assertEquals($expected, $this->asCode($mutation));
    }

    private function asAst($code)
    {
        $lexer = new Lexer(['usedAttributes' => []]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        $ast = $parser->parse("<?php " . $code);
        return $ast[0];
    }

    private function asCode($ast)
    {
        return (new Standard)->prettyPrint([$ast]);
    }
}
