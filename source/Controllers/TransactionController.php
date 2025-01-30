<?php
namespace Source\Controllers;

use Exception;
use Source\Controllers\Controller;
use Source\Expections\TransactionException;
use Source\Expections\ValidationException;
use Source\Models\Transaction;
use Source\Support\Response;
use Source\Support\Validator;

class TransactionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insert(): void
    {
        try {   
            $this->validateInsertFields();

            $transaction = new Transaction();
            $transaction->insert($this->data);

            Response::success("Transação cadastrada com sucesso!", 201, response: $transaction->data());
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(TransactionException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErros());
        } catch(Exception $e) {
            Response::serverError();
        }
    }

    public function update(array $data): void
    {
        try {   
            $this->data["id"] = $data["id"];
            $this->validateUpdateFields();

            $transaction = new Transaction();
            $response = $transaction->edit($this->data);

            Response::success("Transação atualizada com sucesso!", response: $response->data());
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(TransactionException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErros());
        } catch(Exception $e) {
            Response::serverError();
        }
    }

    public function delete(array $data): void
    {
        try {   
            $this->data['id'] = $data['id'];
            $this->validateId();

            (new Transaction())->remove($data['id']);
            Response::success("Transação deletada com sucesso!");
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(TransactionException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErros());
        } catch(Exception $e) {
            Response::serverError();
        }
    }

    public function getById(array $data): void
    {
        try {   
            $this->data['id'] = $data['id'];
            $this->validateId();

            $transaction = new Transaction();
            $response = $transaction->getById($data['id']);

            Response::success("Transação encontrada com sucesso!", response: $response->data());
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(TransactionException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErros());
        } catch(Exception $e) {
            Response::serverError();
        }   
    }

    public function getAll(): void
    {
        $transactions = (new Transaction())->getAll();
        Response::success("Consulta feita com sucesso!", response: $transactions);
    }

    public function getByType(array $data): void
    {
        try {
            $transactions = (new Transaction())->getByType($data['type']);

            Response::success("Consulta feita com sucesso!", response: $transactions);
        } catch(TransactionException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErros());
        } catch(Exception $e) {
            Response::serverError();
        }  
    }

    private function validateInsertFields(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("category_id")
            ->numeric("category_id")
            ->required("type")
            ->required("amount")
            ->numeric("amount")
            ->validate();
    }

    private function validateUpdateFields(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("id")
            ->numeric("id")
            ->required("category_id")
            ->numeric("category_id")
            ->required("type")
            ->required("amount")
            ->numeric("amount")
            ->validate();
    }

    private function validateId(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("id")
            ->numeric("id")
            ->validate();
    }
}