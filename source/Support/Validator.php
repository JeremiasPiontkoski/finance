<?php
namespace Source\Support;

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
        return ['errors' => $this->errors];
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

    public function email(string $field): self
    {
        if (!filter_var($this->data[$field] ?? '', FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$field} deve ser um e-mail válido!");
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
}
