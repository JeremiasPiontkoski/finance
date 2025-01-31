<?php
namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use Source\Expections\UserException;
use Source\Support\Auth;

class User extends DataLayer
{
    public function __construct()
    {
        parent::__construct("users", ['name', 'email', 'password']);
    }

    public function insert(array $data): self
    {
        $this->checkUserByEmail($data['email']);

        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = password_hash($data['password'], PASSWORD_BCRYPT);

        if (!$this->save()) {
            throw new UserException([
                "database" => [
                    $this->fail()->getMessage()
                ]
            ], "Erro no cadastro!", $this->fail()->getCode());
        }

        return $this;
    }

    public function edit(array $data): self
    {
        $this->checkUserByEmail($data['email'], Auth::getData()->id);
        $user = $this->getByEmail($data['email']);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!$user->save()) {
            throw new UserException([
                "database" => [
                    $user->fail()->getMessage()
                ]
            ], "Erro na edição!", $user->fail()->getCode());
        }

        return $user;
    }

    public function getAll(): array
    {
        return $this->find()->fetch(true) ?? [];
    }

    public function login(string $email, string $password): self
    {
        $findedUser = $this->find("email = :email", "email={$email}")->fetch();
        
        if (
            empty($findedUser) ||
            !password_verify($password, $findedUser->password)
        ) {
            throw new UserException([], "Email e/ou senha inválidos!", 401);
        }

        return $findedUser;
    }

    private function getByEmail(string $email): self|array
    {
        return $this->find("email = :email", "email={$email}")->fetch() ?? [];
    }

    private function isEmailUnique(string $email, $id = null): bool
    {
        $findedUser = null;

        if (!$id) {
            $findedUser = $this->find("email = :email", "email={$email}")->fetch();
        }else {
            $findedUser = $this->find("email = :email AND id != :id ", "email={$email}&id={$id}")->fetch();
        }

        if ($findedUser) {
            return false;
        }

        return true;
    }

    private function checkUserByEmail(string $email, int $id = null): void
    {
        if (!$this->isEmailUnique($email, $id)) {
            throw new UserException([
                "email" => ["O email {$email} já está em uso!"]
            ], "Dados inválidos!");
        }
    }
}