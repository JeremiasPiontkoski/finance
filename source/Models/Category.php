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

    public function edit(array $data): self
    {
        $category = $this->getById($data['id']);
        if (empty($category)) {
            throw new CategoryException([
                "id" => [
                    "Id inválido!"
                ]
            ], message: "Erro ao encontrar uma categoria!");   
        }

        if (!$this->isOwner($category)) {
            throw new CategoryException([
                "user" => "Este usuário não tem permissão para editar esta categoria!"
            ], message: "Permissão negada!", code: 403);
        }

        if ($this->existsByName($data['name'])) {
            throw new CategoryException([
                "name" => [
                    "O nome {$data['name']} já está em uso!"
                    ]
            ], "Dados inválidos!");
        }

        $this->id = $data['id'];
        $this->user_id = Auth::getData()->id;
        $this->name = $data['name'];
        
        if (!$this->save()) {
            throw new CategoryException([
                "databse" => [
                    "Erro ao editar a categoria. Verifique os dados e tente novamente!"
                ]
            ], "Erro na edição!");
        }

        return $this;
    }

    public function remove(int $id): bool
    {
        $category = $this->getById($id);
        if (empty($category)) {
            throw new CategoryException([
                "id" => [
                    "Id inválido!"
                ]
            ], message: "Erro ao encontrar uma categoria!");   
        }

        if (!$this->isOwner($category)) {
            throw new CategoryException([
                "user" => "Este usuário não tem permissão para editar esta categoria!"
            ], message: "Permissão negada!", code: 403);
        }

        if (!$category->destroy()) {
            throw new CategoryException([
                "databse" => [
                    "Erro ao deletar a categoria. Verifique os dados e tente novamente!"
                ]
            ], "Erro ao deletar!");
        }

        return true;
    }

    public function getAllByUser(): array
    {
        $userData = Auth::getData();
        return $this->find("user_id = :uid", "uid=$userData->id}")->fetch(true) ?? [];
    }

    public function getById(string $id): self|array
    {
        return $this->findById($id) ?? [];
    }

    private function existsByName(string $name): bool
    {
        $exists = $this->find("name = :name", "name={$name}")->count();

        return $exists > 0;
    }

    private function isOwner(Category $category): bool
    {
        return $category->user_id == Auth::getData()->id;
    }
}