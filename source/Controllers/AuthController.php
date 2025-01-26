<?php
namespace Source\Controllers;

use Exception;
use Source\Expections\ValidationException;
use Source\Models\User;
use Source\Support\Auth;
use Source\Support\Response;
use Source\Support\Validator;

class AuthController extends Controller
{
    public function login(): void
    {
        $data = $this->data;

        try {
            $this->validateLoginFields($data);

            $user = new User();
            $user->email = $data['email'];
            $user->password = $data['password'];

            if (!$user->isLogged()) {
                Response::invalidLogin();
                return;
            }

            $token = Auth::generateToken([
                "id" => $user->id,
                "email" => $user->email
            ]);

            Response::success("Login realizado com sucesso!", response: [
                "id" => $user->id,
                "token" => $token
            ]);
        }catch(ValidationException $e) {
            Response::error($e->getMessage(), response: $e->getErrors());
        }catch(Exception $e) {
            Response::serverError();
        }
    }

    private function validateLoginFields(array $data): void
    {
        $validator = new Validator($data);
        $validator
            ->required("email")
            ->required("password")
            ->email()
            ->validate();
    }
}