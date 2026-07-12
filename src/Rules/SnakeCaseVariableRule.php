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

        // Ignore special variables that cannot/should not be renamed.
        $ignored = [
            'this',
            'GLOBALS',
            '_SERVER',
            '_GET',
            '_POST',
            '_FILES',
            '_COOKIE',
            '_SESSION',
            '_REQUEST',
            '_ENV',
            'argc',
            'argv',
            'http_response_header',
        ];
        $varName = $node->name;
        if (in_array($varName, $ignored, true)) {
            return [];
        }

        if (!preg_match('/^[a-z][a-z0-9]*(_[a-z0-9]+)*$/', $varName)) {
            return [
                RuleErrorBuilder::message(
                    sprintf('Variable $%s must be snake_case.', $varName)
                )->build()
            ];
        }

        return [];
    }
}
