<?php
namespace Source\Support;

use Source\Expections\ValidationException;
use Source\Models\Category;
use Source\Models\User;

class Validator {
    private $data = [];
    private $errors = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function required(string $field): self
    {
        if (empty($this->data[$field])) {
            $this->addError($field, "{$field} é obrigatório!");
        }

        return $this;
    }

    public function email(): self
    {
        if (!filter_var($this->data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $this->addError("email", "Email deve ser um e-mail válido!");
        }

        return $this;
    }

    public function numeric(string $field): self
    {
        if (!is_numeric($this->data[$field] ?? null)) {
            $this->addError($field, "{$field} deve ser numérico!");
        }

        return $this;
    }

    public function min(string $field, int $length): self
    {
        if (strlen($this->data[$field] ?? '') < $length) {
            $this->addError($field, "{$field} deve ter pelo menos {$length} caracteres!");
        }

        return $this;
    }

    public function validate(): void
    {
        if (!empty($this->getErrors())) {
            throw new ValidationException($this->errors);
        }
    }
}
