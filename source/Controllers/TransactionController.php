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

            Response::success("Transação cadastrada com sucesso!", response: $transaction->data());
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
}