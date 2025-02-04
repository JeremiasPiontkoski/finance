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

    /**
     * @OA\Post(
     *     path="/transactions",
     *     summary="Insert Transaction",
     *     tags={"Transaction"},
     *     security={{"TokenJwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_id", "type", "amount"},
     *             @OA\Property(property="category_id", type="integer", example=""),
     *             @OA\Property(property="type", type="string", example=""),
     *             @OA\Property(property="amount", type="integer", format="float", example=""),
     *             @OA\Property(property="description", type="string", example=""),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sucess",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Data"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
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
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(Exception $e) {
            Response::serverError();
        }
    }

    /**
     * @OA\Put(
     *     path="/transactions/{id}",
     *     summary="Update Transaction",
     *     tags={"Transaction"},
     *     security={{"TokenJwt": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Transaction Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_id", "type", "amount"},
     *             @OA\Property(property="category_id", type="integer", example=""),
     *             @OA\Property(property="type", type="string", example=""),
     *             @OA\Property(property="amount", type="integer", format="float", example=""),
     *             @OA\Property(property="description", type="string", example=""),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucess",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Data"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
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
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(Exception $e) {
            Response::serverError();
        }
    }

    /**
     * @OA\Delete(
     *     path="/transactions/{id}",
     *     summary="Delete Transaction By Id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Transaction Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     tags={"Transaction"},
     *     security={{"TokenJwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucess",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Data"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
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
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(Exception $e) {
            Response::serverError();
        }
    }

    /**
     * @OA\Get(
     *     path="/transactions/{id}",
     *     summary="Get Transaction By Id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Transaction Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     tags={"Transaction"},
     *     security={{"TokenJwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucess",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Data"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
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
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(Exception $e) {
            Response::serverError();
        }   
    }

    /**
     * @OA\Get(
     *     path="/transactions",
     *     summary="Get All Transactions By UserId",
     *     tags={"Transaction"},
     *     security={{"TokenJwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucess",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getAll(): void
    {
        $transactions = (new Transaction())->getAll();
        Response::success("Consulta feita com sucesso!", response: $transactions);
    }

    /**
     * @OA\Get(
     *     path="/transactions/type/{type}",
     *     summary="Get Transaction By Type",
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Type Transaction",
     *         @OA\Schema(type="string")
     *     ),
     *     tags={"Transaction"},
     *     security={{"TokenJwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucess",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Data"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getByType(array $data): void
    {
        try {
            $transactions = (new Transaction())->getByType($data['type']);

            Response::success("Consulta feita com sucesso!", response: $transactions);
        } catch(TransactionException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
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