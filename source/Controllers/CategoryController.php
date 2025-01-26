<?php
namespace Source\Controllers;

use Dotenv\Repository\RepositoryInterface;
use Exception;
use Source\Expections\CategoryException;
use Source\Expections\ValidationException;
use Source\Models\Category;
use Source\Support\Auth;
use Source\Support\Response;
use Source\Support\Validator;

class CategoryController extends Controller
{
    public function insert(): void
    {
        try {
            $this->validateInserFields();

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

    private function validateInserFields(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("name")
            ->validate();
    }
}