<?php
namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use Source\Expections\TransactionException;
use Source\Support\Auth;

class Transaction extends DataLayer
{
    private array $types = ["receita", "despesa"];
    private object $loggedUserData;

    public function __construct()
    {
        parent::__construct("transactions", ["user_id", "type", "amount"]);

        $this->loggedUserData = Auth::getData();
    }

    public function insert(array $data): self
    {
        $this->validateType($data['type']);
        $this->validateCategoryExistsAndUserIsOwner($data['category_id']);

        $this->user_id = $this->loggedUserData->id;
        $this->category_id = $data['category_id'];
        $this->type = $data['type'];
        $this->amount = $data['amount'];
        $this->description = $data['description'] ?? null;

        if (!$this->save()) {
            throw new TransactionException([
                "database" => [
                   $this->fail()->getMessage()
                ]
            ], "Erro no cadastro!", $this->fail()->getCode());
        }

        return $this;
    }

    private function validateType(string $type): void
    {
        if (!in_array($type, $this->types)) {
            throw new TransactionException([
                "type" => ["O tipo de transação deve ser 'receita' ou 'despesa''"]
            ], "Dados inválidos!");
        }
    }

    private function validateCategoryExistsAndUserIsOwner(int $category_id): void
    {
        $category = (new Category())->getByIdAndUserId($category_id, $this->loggedUserData->id);

        if (empty($category)) {
            throw new TransactionException([
                "category_id" => ["Categoria inválida!"]
            ], "Dados inválidos!");
        }
    }
}