<?php
namespace Source\Expections;

use Exception;

class TransactionException extends Exception
{
    private array $erros;

    public function __construct(array $erros, string $message, int $code = 400)
    {
        parent::__construct($message, $code);

        $this->erros = $erros;
    }

    public function getErros(): array
    {
        return $this->erros;
    }
}