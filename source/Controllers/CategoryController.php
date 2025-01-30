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
    public function insert(): void
    {
        try {
            $this->validateInsertFields();

            $category = new Category();
            $category->insert($this->data);

            Response::success("Categoria criada com sucesso!", response: $category->data());
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), response: $e->getErrors());
        } catch (CategoryException $e) {
            Response::error($e->getMessage(), $e->getCode(), response: $e->getErrors());
        } catch (Exception $e) {
            Response::serverError();
        }
    }

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

    public function delete(array $data): void
    {
        try {
            $this->validateDeleteFields($data);

            (new Category())->remove($data['id']);

            Response::success("Categoria deletada com sucesso!");
        } catch (ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        }  catch (CategoryException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        }
        catch (Exception $e) {
            Response::serverError();
        }
    }

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