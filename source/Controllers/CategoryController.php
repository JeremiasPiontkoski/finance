<?php
namespace Source\Controllers;

use Exception;
use Source\Expections\CategoryException;
use Source\Expections\ValidationException;
use Source\Models\Category;
use Source\Support\Response;
use Source\Support\Validator;

class CategoryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/categories",
     *     summary="Insert Category",
     *     tags={"Category"},
     *     security={{"TokenJwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example=""),
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

            $category = new Category();
            $category->insert($this->data);

            Response::success("Categoria criada com sucesso!", 201, response: $category->data());
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), response: $e->getErrors());
        } catch (CategoryException $e) {
            Response::error($e->getMessage(), $e->getCode(), response: $e->getErrors());
        } catch (Exception $e) {
            Response::serverError();
        }
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     summary="Update Category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     tags={"Category"},
     *     security={{"TokenJwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example=""),
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
            $this->data['id'] = $data['id'];
            $this->validateUpdateFields();

            $category = new Category();
            $response = $category->edit($this->data);

            Response::success("Categoria atualizada com sucesso!", response: $response->data());
        } catch (ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch (CategoryException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        }
         catch (Exception $e) {
            Response::serverError();
        }
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Delete Category By Id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     tags={"Category"},
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
            $this->validateDeleteFields($data);

            (new Category())->remove($data['id']);

            Response::success("Categoria deletada com sucesso!");
        } catch (ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch (CategoryException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch (Exception $e) {
            Response::serverError();
        }
    }

    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Get All Categories By UserId",
     *     tags={"Category"},
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
    public function getAllByUser(): void
    {
        $categories = (new Category())->getAllByUser();
        $data = array_map(function ($category) {
            return $category->data();
        }, $categories);

        Response::success("Consulta feita com sucesso!", response: $data);
    }

    private function validateInsertFields(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("name")
            ->validate();
    }

    private function validateUpdateFields(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("id")
            ->numeric("id")
            ->required("name")
            ->validate();
    }

    private function validateDeleteFields(array $data): void
    {
        $validator = new Validator($data);
        $validator
            ->required("id")
            ->numeric("id")
            ->validate();
    }
}