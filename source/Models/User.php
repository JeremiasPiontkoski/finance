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

    /**
     * Verifica as regras de negócio e insere um novo usuário no banco
     * @param array $data Array com dados da requisição
     * @return self Irá retornar o novo usuário
     * @throws UserException Irá lançar uma nova excessão caso tenha dados incorretos ou problemas no banco
     */
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

    /**
     * Verifica as regras de negócio e edita um usuário no banco
     * @param array $data Array com dados da requisição
     * @return self Irá retornar o usuário editado
     * @throws UserException Irá lançar uma nova excessão caso tenha dados incorretos ou problemas no banco
     */
    public function edit(array $data): self
    {
        $this->checkUserByEmail($data['email'], Auth::getData()->id);
        $user = $this->findById(Auth::getData()->id);

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

    /**
     * Irá retornar todos os usuários cadastrados
     * @return array Array com os usuário do sistema
     */
    public function getAll(): array
    {
        return $this->find()->fetch(true) ?? [];
    }

    /**
     * Realiza verificações e o login no sistema
     * @param string $email Email do usuário
     * @param string $password Senha do usuário
     * @return self Dados do usuário logado
     * @throws UserException Irá lançar uma excessão caso os dados estejam incorretos
     */
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

    /**
     * Busca um usuário por email
     * @param string $email Email do usuário
     * @return self Retorna o usuário encontrado
     * @return array Irá retornar uma array vazio[] caso não tenha um usuário com o email informado
     */
    private function getByEmail(string $email): self|array
    {
        return $this->find("email = :email", "email={$email}")->fetch() ?? [];
    }

    /**
     * Verifica se o email é único
     * Sem id: Irá verificar se o email é único sem restrições
     * Com id: Irá verificar se o email é único com a restrição do id do informado ser diferente do id informado
     * @param string $email Email a ser verificado
     * @param int $id Id do usuário a ser verificado
     * @return bool False caso exista um usuário com o email e True caso não exista um usuário com o email
     */
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

    /**
     * Verifica se existe um usuário com o email cadastrado no banco
     * @param string $email Email a ser verificado
     * @param int $id Id do usuário para seja necessário
     * @return void Não irá ter retorno caso não tenha um usuário com o email cadatrado
     * @throws UserException Irá lançar uma excessão caso tenha um usuário com o email cadastrado
     */
    private function checkUserByEmail(string $email, int $id = null): void
    {
        if (!$this->isEmailUnique($email, $id)) {
            throw new UserException([
                "email" => ["O email {$email} já está em uso!"]
            ], "Dados inválidos!");
        }
    }
}