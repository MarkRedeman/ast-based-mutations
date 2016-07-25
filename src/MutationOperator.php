<?php

declare(strict_types=1);

namespace Renamed;

use PhpParser\Node;

interface MutationOperator {

    public function mutate(Node $node);
}
