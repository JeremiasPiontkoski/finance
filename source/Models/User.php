<?php
namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use Source\Expections\UserException;

class User extends DataLayer
{
    public function __construct()
    {
        parent::__construct("users", ['name', 'email', 'password']);
    }

    public function insert(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = password_hash($data['password'], PASSWORD_BCRYPT);

        if (!$this->save()) {
            return null;
        }

        return $this;
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

    public function isEmailUnique(string $email, $id = null): bool
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
}