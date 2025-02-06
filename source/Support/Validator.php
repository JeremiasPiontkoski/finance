<?php
namespace Source\Support;

use Source\Expections\ValidationException;

/**
 * Classe responsável pela validação de dados
 * @property array $data Dados a serem verificados
 * @property array $errors Erros encontrados nos dados
 */
class Validator {
    private $data = [];
    private $errors = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * Adiciona erros encontrados
     * @param string $field Campo em que possui um erro
     * @param string $message Mensagem de erro
     * @return void Não possui retorno
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Retorna os erros
     * @return array Retorna os erros
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retorna se há erros
     * @return bool True se tiver errros e False caso não tenha erros
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Verifica se o campo obrigatório possui dados
     * @param string $field Campo a ser verificado
     * @return self Retorna a instância da classe
     */
    public function required(string $field): self
    {
        if (empty($this->data[$field])) {
            $this->addError($field, "{$field} é obrigatório!");
        }

        return $this;
    }

    /**
     * Verifica se o email é válido
     * @return self Retorna a instância da classe
     */
    public function email(): self
    {
        if (!filter_var($this->data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $this->addError("email", "Email deve ser um e-mail válido!");
        }

        return $this;
    }

    /**
     * Verifica se um valor é numérico
     * @param string $field Campo a ser verificado
     * @return self Retorna uma instância da classe
     */
    public function numeric(string $field): self
    {
        if (!is_numeric($this->data[$field] ?? null)) {
            $this->addError($field, "{$field} deve ser numérico!");
        }

        return $this;
    }

    /**
     * Verifica se uma string tem o tamanho mínimo informado
     * @param string $field Campo a ser verificado
     * @param int $length Tamanho mínimo
     * @return self Retorna uma instância da classe
     */
    public function min(string $field, int $length): self
    {
        if (strlen($this->data[$field] ?? '') < $length) {
            $this->addError($field, "{$field} deve ter pelo menos {$length} caracteres!");
        }

        return $this;
    }

    /**
     * Método responsável por iniciar as validações
     * @return void Não tem retorno
     */
    public function validate(): void
    {
        if (!empty($this->getErrors())) {
            throw new ValidationException($this->errors);
        }
    }
}
