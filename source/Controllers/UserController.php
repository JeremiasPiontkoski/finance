<?php
namespace Source\Controllers;

use Exception;
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

    public function insert(array $data): void
    {
        try {
            $data = $this->data;
            $this->validateInsertFields($data);

            $user = new User();
            if (empty($user->insert($data))) return;

            Response::success("Usuário criado com sucesso!", response: [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email
            ]);
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(Exception $e) {
            Response::error($e->getMessage());
        }
    }


    private function validateInsertFields(array $data): void
    {
        $validator = new Validator($data);
        $validator
            ->required("name")
            ->required("email")
            ->email()
            ->uniqueEmail()
            ->required("password")
            ->min("password", 6)
            ->validate();
    }
}