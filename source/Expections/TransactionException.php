<?php
namespace Source\Expections;

use Exception;

class TransactionException extends Exception
{
    private array $errors;

    public function __construct(array $errors, string $message, int $code = 400)
    {
        parent::__construct($message, $code);

        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}