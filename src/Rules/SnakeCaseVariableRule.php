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
        // Local variables
        if ($node instanceof Node\Expr\Variable) {
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
        }

        // Class properties
        if ($node instanceof Node\Stmt\Property) {
            $propNames = array_map(
                static fn(\PhpParser\Node\PropertyItem $prop): string => $prop->name->toString(),
                $node->props
            );

            foreach ($propNames as $propName) {
                if (!preg_match('/^[a-z]+(_[a-z]+)*$/', $propName)) {
                    $errors[] = RuleErrorBuilder::message(
                        sprintf('Property $%s must be snake_case.', $propName)
                    )->build();
                }
            }
            return $errors;
        }
        return [];
    }
}
