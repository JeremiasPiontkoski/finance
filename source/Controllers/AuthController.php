<?php
namespace Source\Controllers;

use Exception;
use OpenApi\Annotations\OpenApi;
use OpenApi\Attributes\OpenApi as AttributesOpenApi;
use Source\Expections\UserException;
use Source\Expections\ValidationException;
use Source\Models\User;
use Source\Support\Auth;
use Source\Support\Response;
use Source\Support\Validator;


class AuthController extends Controller
{
    /**
     * 
     * @OA\Post(
     *     path="/auth",
     *     summary="Login",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example=""),
     *             @OA\Property(property="password", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucessfull Login",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description=""
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(): void
    {
        $data = $this->data;

        try {
            $this->validateLoginFields($data);

            $user = (new User())->login($data['email'],  $data['password']);

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
        }catch(UserException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        }
        catch(Exception $e) {
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