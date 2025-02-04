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

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Insert User",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="email", type="string", format="email", example=""),
     *             @OA\Property(property="password", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sucess",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Data"
     *     )
     * )
     */
    public function insert(): void
    {
        try {
            $this->validateInsertFields($this->data);

            $user = new User();
            $user->insert($this->data);

            Response::success("Usuário criado com sucesso!", 201, response: $this->getResponseUser($user));
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(UserException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(Exception $e) {
            Response::error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/users",
     *     summary="Update User",
     *     tags={"User"},
     *     security={{"TokenJwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="email", type="string", format="email", example=""),
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
    public function update(): void
    {
        try {
            $this->validateUpdateFields();

            $user = (new User())->edit($this->data);
            Response::success("Usuário editado com sucesso!", response: $this->getResponseUser($user));
        } catch(ValidationException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(UserException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch(Exception $e) {
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
            ->required("name")
            ->required("email")
            ->email()
            ->validate();
    }

    private function getResponseUser(User $user): array
    {
        return [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email
        ];
    }
}