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
        $this->checkCategoryByName($data['name']);

        $this->user_id = Auth::getData()->id;
        $this->name = $data['name'];

        if (!$this->save()) {
            throw new CategoryException([
                "database" => [
                    "Erro ao cadastrar uma categoria nova. Verifique os dados e tente novamente!"
                ]
            ], "Erro no cadastro!");
        }

        return $this;
    }

    public function edit(array $data): self
    {
        $category = $this->checkCategoryById($data['id']);

        $this->checkIsOwner($category);
        $this->checkCategoryByName($data['name']);

        $this->id = $data['id'];
        $this->user_id = Auth::getData()->id;
        $this->name = $data['name'];
        
        if (!$this->save()) {
            throw new CategoryException([
                "database" => [
                    "Erro ao editar a categoria. Verifique os dados e tente novamente!"
                ]
            ], "Erro na edição!");
        }

        return $this;
    }

    public function remove(int $id): void
    {
        $category = $this->checkCategoryById($id);
        
        $this->checkIsOwner($category);

        if (!$category->destroy()) {
            throw new CategoryException([
                "database" => [
                    "Erro ao deletar a categoria. Verifique os dados e tente novamente!"
                ]
            ], "Erro ao deletar!");
        }
    }

    public function getAllByUser(): array
    {
        $userData = Auth::getData();
        return $this->find("user_id = :uid", "uid=$userData->id}")->fetch(true) ?? [];
    }

    public function getById(int $id): self|array
    {
        return $this->findById($id) ?? [];
    }

    public function getByIdAndUserId(int $category_id, int $user_id): self|array
    {
        return $this->find("id = :id AND user_id = :uid", "id={$category_id}&uid={$user_id}")->fetch() ?? [];
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

    private function checkCategoryByName(string $name): void
    {
        if ($this->existsByName($name)) {
            throw new CategoryException([
                "name" => [
                    "O nome {$name} já está em uso!"
                    ]
            ], "Dados inválidos!");
        }
    }

    private function checkCategoryById(int $id): self
    {
        $category = $this->getById($id);
        if (empty($category)) {
            throw new CategoryException([
                "id" => [
                    "Id inválido!"
                ]
            ], message: "Erro ao encontrar uma categoria!");   
        }

        return $category;
    }

    private function checkIsOwner(Category $category): void
    {
        if (!$this->isOwner($category)) {
            throw new CategoryException([
                "user" => "Este usuário não tem permissão para editar esta categoria!"
            ], message: "Permissão negada!", code: 403);
        }
    }
}