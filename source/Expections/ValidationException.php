<?php
namespace Source\Expections;

use Exception;

class ValidationException extends Exception
{
    private array $errors;

    public function __construct(array $errors, $message = "Dados inválidos!", $code = 400)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}