<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation;

use Wepesi\Core\Application;
use Wepesi\Core\Exceptions\ValidationException;
use Wepesi\Core\Http\Response;
use Wepesi\Core\Resolver\Option;
use Wepesi\Core\Resolver\OptionsResolver;
use Wepesi\Core\Validation\Providers\Contracts\MessageBuilderContracts;

/**
 *
 */
final class Validate
{
    /**
     * @var array
     */
    private array $errors;
    /**
     * @var bool
     */
    private bool $passed;

    /**
     * @var MessageBuilderContracts
     */
    private MessageBuilderContracts $message;

    public function __construct()
    {
        $this->errors = [];
        $this->passed = false;
        $this->message = Application::make(MessageErrorBuilder::class);
    }

    /**
     * Validate the data source against the schema
     *
     * @param array $resource data source where the information will be extracted;
     * @param array $schema data schema
     *
     * @return void
     * @throws ValidationException
     */
    function check(array $resource, array $schema): void
    {
        $this->errors = [];
        $option_resolver = [];
        /**
         * use of Option resolver to catch all undefined keys
         * on the source data
         */
        foreach ($resource as $item => $response) {
            $option_resolver[] = Application::make(Option::class, [$item]);
        }

        $resolver = Application::make(OptionsResolver::class, [$option_resolver]);
        $options = $resolver->resolve($schema);
        $exceptions = $options['InvalidArgumentException'] ?? false;
        if ($exceptions) {
            $this->message
                ->type('object.unknown')
                ->message($exceptions->getMessage())
                ->label('exception');
            $this->addError($this->message);
        } else {
            foreach ($schema as $item => $rules) {
                if (!is_array($rules) && is_object($rules)) {
                    if (!$rules->generate()) {
                        Response::setStatusCode(500);
                        throw new ValidationException('This rule is not a valid! method generate does not exist');
                    }
                    $rules = $rules->generate();
                }
                $class_namespace = array_keys($rules)[0];
                if ($class_namespace == 'any') continue;
                $validator_class = str_replace('Rules', 'Validator', $class_namespace);

                $instance = Application::make($validator_class, [
                    $item,
                    $resource
                ]);

                foreach ($rules[$class_namespace] as $method => $params) {
                    if (method_exists($instance, $method)) {
                        Application::container()->call([$instance, $method], [$params]);
                    }
                }
                $result = $instance->result();
                if (count($result) > 0) {
                    $this->errors = array_merge($this->errors, $result);
                }
            }
            if (count($this->errors) == 0) {
                $this->passed = true;
            }
        }
    }

    /**
     * Add an error to the error list
     *
     * @param MessageBuilderContracts $item
     * @return void
     */
    private function addError(MessageBuilderContracts $item): void
    {
        $this->errors[] = $item->generate();
    }

    /**
     * Get validation errors
     * 1. if the validation passed, the array will be empty
     * 2. if the validation failed, the array will contain the errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function passed(): bool
    {
        return $this->passed;
    }
}