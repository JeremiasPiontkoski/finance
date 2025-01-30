<?php
namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use PHPUnit\Framework\EmptyStringException;
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

    public function edit(array $data): self
    {
        $transaction = $this->checkTransactionById($data['id']);
        $this->checkIsOwner($transaction);
        $this->validateType($data['type']);
        $this->validateCategoryExistsAndUserIsOwner($data['category_id']);

        $transaction->category_id = $data['category_id'];
        $transaction->type = $data['type'];
        $transaction->amount = $data['amount'];
        $transaction->description = $data['description'] ?? null;

        if (!$transaction->save()) {
            throw new TransactionException([
                "database" => [
                   $transaction->fail()->getMessage()
                ]
            ], "Erro na edição!", $transaction->fail()->getCode());
        }

        return $transaction;
    }

    public function remove(int $id): void
    {
        $transaction = $this->checkTransactionById($id);
        $this->checkIsOwner($transaction);

        if (!$transaction->destroy()) {
            throw new TransactionException([
                "database" => [
                    "Erro ao deletar a transação. Verifique os dados e tente novamente!"
                ]
            ], "Erro ao deletar!");
        }
    }

    public function getById(int $id): self
    {
        $transaction = $this->checkTransactionById($id);
        $this->checkIsOwner($transaction);

        if (!empty($transaction->category_id)) {
            $category = (new Category())->findById($transaction->category_id);
            $transaction->category = $category->data();
        }

        return $transaction;
    }

    public function getAll(): array
    {
        $transactions = $this->getAllByUser(); 

        if (empty($transactions)) {
            return [];
        }

        $response = $this->getCategoriesByTransactions($transactions);
        return $response;
    }

    public function getAllByUser(): array
    {
        return $this->find("user_id = :uid", "uid={$this->loggedUserData->id}")->fetch(true) ?? [];
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
                "category_id" => ["Você não tem acesso a esta categoria!"]
            ], "Dados inválidos!");
        }
    }

    private function isOwner(Transaction $transaction): bool
    {
        return $transaction->user_id == Auth::getData()->id;
    }

    private function checkIsOwner(Transaction $transaction): void
    {
        if (!$this->isOwner($transaction)) {
            throw new TransactionException([
                "user" => "Este usuário não tem permissão para editar esta transação!"
            ], message: "Permissão negada!", code: 403);
        }
    }

    private function checkTransactionById(int $id): self
    {
        $transaction = $this->findById($id);
        if (empty($transaction)) {
            throw new TransactionException([
                "id" => [
                    "Id inválido!"
                ]
            ], message: "Erro ao encontrar uma transação!");   
        }

        return $transaction;
    }

    private function getCategoriesByTransactions(array $transactions): array
    {
        $response = array_map(function($transaction) {
            if (!empty($transaction->category_id)) {
                $category = (new Category())->findById($transaction->category_id);
                $transaction->category = $category->data();
            }

            return $transaction->data();
        }, $transactions);

        return $response;
    }
}