<?php

declare(strict_types=1);

namespace Renamed;

use Closure;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Renamed\Mutations\Multiplication;

final class MutateSourceCode
{
    private $operators;

    public function __construct(MutationOperator ...$operators)
    {
        $this->operators = $operators;
    }

    /**
     * @param string $source code to be mutated
     * @param Closure $whenMutationApplied callback that is called
     * each time a mutation has been applied
     */
    public function mutate(string $source, Closure $whenMutationApplied)
    {
        $ast = $this->generateASTFromCode($source);

        // Setup the mutation generator, which will apply a mutation once it's
        // been generated
        // The ApplyMutation object will apply the mutation and then call the
        // storeAppliedMutations closure so that we can check that the mutations
        // have successfully been applied
        $generator = new GenerateMutations(
            $this->applyMutationAfterGeneration(
                new ApplyMutation($ast),
                $whenMutationApplied
            ),
            ...$this->operators
        );

        // Generate and store the applied mutations
        $generator->generate($ast);
    }

    /**
     * Returns the AST of a given source
     * @param string $source the original source code
     */
    private function generateASTFromCode(string $source) : array
    {
        $lexer = new Lexer(['usedAttributes' => ['startline', 'endline']]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        return $parser->parse($source);
    }

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
}
