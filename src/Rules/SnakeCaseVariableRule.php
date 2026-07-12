<?php

declare(strict_types=1);

namespace Wepesi\Rules;

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

class SnakeCaseVariableRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Expr\Variable::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!is_string($node->name)) {
            return [];
        }

        $varName = $node->name;
        if ($varName === 'this') {
            return [];
        }

        if (!preg_match('/^[a-z]+(_[a-z]+)*$/', $varName)) {
            return [
                RuleErrorBuilder::message(
                    sprintf('Variable $%s must be snake_case.', $varName)
                )->build()
            ];
        }

        return [];
    }
}
