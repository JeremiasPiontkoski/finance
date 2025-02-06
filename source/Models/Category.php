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

    /**
     * Realiza as regras de negócio e insere o usuário no sistema
     * @param array $data Array com os dados da requisição
     * @return self Irá retornar os dados do novo usuário inserido no sistema
     * @throws CategoryException Irá lançar uma excessão caso algum dado esteja incorreto ou problemas ao inserir no banco 
     */
    public function insert(array $data): self
    {
        $this->checkCategoryByName($data['name']);

        /**
         * @var int Pega o id do usuário logado
         */
        $this->user_id = Auth::getData()->id;
        $this->name = $data['name'];

        if (!$this->save()) {
            throw new CategoryException([
                "database" => [
                    $this->fail()->getMessage()
                ]
            ], "Erro no cadastro!", $this->fail()->getCode());
        }

        return $this;
    }

    /**
     * Realiza as regras de negócio e atualiza o usuário logado
     * @param array $data Array com os dados da requisição
     * @return self Retorna o usuário editado
     * @throws CategoryException Irá lançar uma excessão caso algum dado esteja incorreto ou problemas ao editar no banco 
     */
    public function edit(array $data): self
    {
        $category = $this->checkCategoryById($data['id']);

        $this->checkIsOwner($category);
        $this->checkCategoryByName($data['name']);

        $category->name = $data['name'];
        
        if (!$category->save()) {
            throw new CategoryException([
                "databse" => [
                    $category->fail()->getMessage()
                ]
            ], "Erro na edição!", $category->fail()->getCode());
        }

        return $category;
    }

    /**
     * Remove uma categoria com base no id informado e se o usuário for dono dela
     * @param int $id Id da categoria a ser deletada
     * @param void Caso a categoria seja deletada, a função não irá retornar nenhum resultado
     * @throws CategoryException Irá lançar uma excessão caso algum dado esteja incorreto ou problemas ao deletar no banco
     */
    public function remove(int $id): void
    {
        $category = $this->checkCategoryById($id);
        
        $this->checkIsOwner($category);

        if (!$category->destroy()) {
            throw new CategoryException([
                "database" => [
                    $this->fail()->getMessage()
                ]
            ], "Erro ao deletar!", $this->fail()->getCode());
        }
    }

    /**
     * Retorna todas as categorias do usuário logado
     * @return array Irá retornar um array com as categorias do usuário ou um array vazio caso o usuário não tenha categorias cadastradas
     */
    public function getAllByUser(): array
    {
        $userData = Auth::getData();
        return $this->find("user_id = :uid", "uid=$userData->id}")->fetch(true) ?? [];
    }

    /**
     * Retorna uma categoria por id
     * @param int $id Id da categoria
     * @return self Irá retornar a categoria encontrada
     * @return array Irá retornar uma array vazio[] caso não seja encontrada a categoria
     */
    public function getById(int $id): self|array
    {
        return $this->findById($id) ?? [];
    }

    /**
     * Irá procurar uma categoria por um id e pelo id do usuário da categoria
     * @param int $category_id Id da categoria
     * @param int $user_id Id do usuário logado no sistema
     * @return self Irá retornar a categoria caso seja encontrada uma categoria
     * @return array Irá um array vazio[] caso não seja encontrada uma categoria
     */
    public function getByIdAndUserId(int $category_id, int $user_id): self|array
    {
        return $this->find("id = :id AND user_id = :uid", "id={$category_id}&uid={$user_id}")->fetch() ?? [];
    }

    /**
     * Verifica se o usuário logado possui uma categoria cadastrada com um determinado nome
     * @param string $name Nome da categoria a ser verificada
     * @return bool Irá retornar true para caso tenha uma categoria com o nome cadastrada e false caso não tenha
     */
    private function existsByNameAndUser(string $name): bool
    {
        $user_id = Auth::getData()->id;
        $exists = $this->find("name = :name AND user_id = :uid", "name={$name}&uid={$user_id}")->count();

        return $exists > 0;
    }

    /**
     * Verifica se o user_id da categoria é o mesmo que o id do usuário logado
     * @param Category $category Categoria a ser verificada
     * @return bool Retorna true se o id for o mesmo e falso quando o id não for o mesmo
     */
    private function isOwner(Category $category): bool
    {
        return $category->user_id == Auth::getData()->id;
    }

    /**
     * Verifica se o nome da categoria cadastrada está disponível
     * @param string $name O nome da categoria a ser verificado
     * @return void Não irá ter retorno caso não exista uma categoria com o nome informado cadastrada
     * @throws CategoryException Irá lançar uma excessão caso já tenha uma categoria cadastrada com o nome informado
     */
    private function checkCategoryByName(string $name): void
    {
        if ($this->existsByNameAndUser($name)) {
            throw new CategoryException([
                "name" => [
                    "O nome {$name} já está em uso!"
                    ]
            ], "Dados inválidos!");
        }
    }

    /**
     * Verifica se existe uma categoria com o id informado no banco
     * @param int $id Id da categoria
     * @return self Irá retornar a categoria encontrada
     * @throws CategoryException Irá lançar uma excessão caso não tenha uma categoria cadastrada no banco com o id informado
     */
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

    /**
     * Verifica se o usuário logado é dono da categoria
     * @param Category $category Categoria que será verificada
     * @param void Não irá ter retorno caso o usuário logado seja dono da categoria
     * @throws CategoryExpetion Irá lançar uma excessão caso o usuário logado não seja o dono da categoria
     */
    private function checkIsOwner(Category $category): void
    {
        if (!$this->isOwner($category)) {
            throw new CategoryException([
                "user" => "Este usuário não tem permissão para editar esta categoria!"
            ], message: "Permissão negada!", code: 403);
        }
    }
}