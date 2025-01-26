<?php
namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use Source\Expections\CategoryException;
use Source\Support\Auth;

class Category extends DataLayer
{
    public function __construct()
    {
        parent::__construct("categories", ["user_id", "name"]);
    }

    public function insert(array $data): self
    {
        if ($this->existsByName($data['name'])) {
            throw new CategoryException([
                "name" => [
                    "O nome {$data['name']} já está em uso!"
                    ]
            ], "Dados inválidos!");
        }

        $this->user_id = Auth::getData()->id;
        $this->name = $data['name'];

        if (!$this->save()) {
            throw new CategoryException([
                "databse" => [
                    "Erro ao cadastrar uma categoria nova. Verifique os dados e tente novamente!"
                ]
            ], "Erro no cadastro!");
        }

        return $this;
    }

    public function existsByName(string $name): bool
    {
        $exists = $this->find("name = :name", "name={$name}")->count();

        return $exists > 0;
    }
}