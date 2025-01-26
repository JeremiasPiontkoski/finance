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
        $this->checkCategoryName($data['name']);
        $this->setData($data);

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
        $category = $this->checkCategoryExists($data['id']);
        $this->checkCategoryName($data['name']);
        $this->checkOwnerPermission($category);

        $this->setData($data);
        
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
        $category = $this->checkCategoryExists($id);
        $this->checkOwnerPermission($category);

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
        return $this->find("name = :name", "name={$name}")->count() > 0;
    }

    private function isOwner(Category $category): bool
    {
        return $category->user_id == Auth::getData()->id;
    }

    private function checkCategoryExists(int $id): self
    {
        $category = $this->getById($id);
        if (empty($category)) {
            throw new CategoryException([
                "id" => ["Id inválido!"]
            ], message: "Erro ao encontrar uma categoria!");
        }

        return $category;
    }

    private function checkOwnerPermission(Category $category): void
    {
        if (!$this->isOwner($category)) {
            throw new CategoryException([
                "user" => "Este usuário não tem permissão para editar ou deletar esta categoria!"
            ], message: "Permissão negada!", code: 403);
        }
    }

    private function checkCategoryName(string $name): void
    {
        if ($this->existsByName($name)) {
            throw new CategoryException([
                "name" => ["O nome {$name} já está em uso!"]
            ], "Dados inválidos!");
        }
    }

    private function setData(array $data): void
    {
        $this->user_id = Auth::getData()->id;
        $this->name = $data['name'];
    }
}
