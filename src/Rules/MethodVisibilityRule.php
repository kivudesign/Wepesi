<?php

declare(strict_types=1);

namespace Wepesi\Rules;

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

class MethodVisibilityRule implements Rule
{
    public function getNodeType(): string
    {
        // This rule applies to both methods and properties
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        $parent = $node->getAttribute('parent');

        // Handle methods
        if ($node instanceof Node\Stmt\ClassMethod) {
            // Abstract class: no private methods
            if ($parent instanceof Node\Stmt\Class_ && $parent->isAbstract() && $node->isPrivate()) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('Private method %s not allowed in abstract classes.', $node->name)
                )->build();
            }

            // Interface: only public
            if ($parent instanceof Node\Stmt\Interface_ && !$node->isPublic()) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('Interface method %s must be public.', $node->name)
                )->build();
            }

            // Trait: discourage public unless documented
            if ($parent instanceof Node\Stmt\Trait_ && $node->isPublic()) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('Trait method %s should not be public unless explicitly documented.', $node->name)
                )->build();
            }
        }

        // Handle properties
        if ($node instanceof Node\Stmt\Property) {
            // Interfaces cannot have properties
            if ($parent instanceof Node\Stmt\Interface_) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('Property %s not allowed in interfaces.', implode(', ', $node->props))
                )->build();
            }

            // Abstract classes: properties should not be private if intended for subclass use
            if ($parent instanceof Node\Stmt\Class_ && $parent->isAbstract() && $node->isPrivate()) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf('Private property %s may limit subclass extensibility.', implode(', ', $node->props))
                )->build();
            }
        }

        return $errors;
    }
}
