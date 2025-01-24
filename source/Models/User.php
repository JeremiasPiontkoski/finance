<?php
namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

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

    public function getAll()
    {
        return $this->find()->fetch(true) ?? [];
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