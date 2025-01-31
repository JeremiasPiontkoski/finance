<?php
namespace Source\Controllers;

use Exception;
use Source\Expections\UserException;
use Source\Expections\ValidationException;
use Source\Models\User;
use Source\Support\Response;
use Source\Support\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insert(): void
    {
        try {
            $this->validateInsertFields($this->data);

            $user = new User();
            $user->insert($this->data);

            Response::success("Usuário criado com sucesso!", 201, response: [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email
            ]);
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(UserException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        }
        catch(Exception $e) {
            Response::error($e->getMessage());
        }
    }

    private function validateInsertFields(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("name")
            ->required("email")
            ->email()
            ->required("password")
            ->min("password", 6)
            ->validate();
    }

    private function validateUpdateFields(): void
    {
        $validator = new Validator($this->data);
        $validator
            ->required("id")
            ->numeric("id")
            ->required("name")
            ->required("email")
            ->email()
            ->validate();
    }
}