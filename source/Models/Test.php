<?php
namespace Source\Models;


use CoffeeCode\DataLayer\Connect;
use PDO;
use PHPUnit\Framework\TestCase;
use Source\Controllers\AuthController;
use Source\Controllers\CategoryController;
use Source\Controllers\TransactionController;
use Source\Controllers\UserController;
use Source\Support\Auth;

/**
 * Classe que os testes devem herdar para ter métodos essências dos bancos
 * 
 * @property PDO $pdo Instancia da conexão pdo
 * @property string $token Token de autenticação
 */
class Test extends TestCase
{
    protected PDO $pdo;
    protected string $token;

    /**
     * Pega a conexão atual do banco e salva no atributo pdo
     * Ativa a transação no banco de dados
     * Gera um token de autenticação
     * Gera os dados do usuário para testes
     */
    protected function setUp(): void
    {
        $this->pdo = Connect::getInstance();
        $this->pdo->beginTransaction();

        $this->makeToken();
        $this->generateDataAuth();
    }

    /**
     * Realiza o rollBack do banco de dados após o teste ser executado
     */
    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * Insere um usuário para ser usado em testes
     * @param string $name Nome que será inserido para testes
     * @param string $email Email que será inserido para testes
     * @param string $password Senha que será inserida para testes
     * @return array Retorna o array da responta da requisição
     */
    protected function makeUser(string $name = "nameForTest", string $email = "emailForTest@gmail.com", string $password = "12345678"): array
    {
        $userController = new UserController();
        $userController->data = [
            "name" => $name,
            "email" => $email,
            "password" => $password
        ];

        ob_start();
        $userController->insert();
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }

    /**
     * Cria uma nova categoria para testes
     * @param string $name Nome da nova categoria
     * @return array Retorna os dados da requisição
     */
    protected function makeCategory(string $name = "nameCategoryForTest"): array
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => $name
        ];

        ob_start();
        $categoryController->insert();
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }

    /**
     * Cria uma nova transação para testes
     * @param string $name Nome da nova categoria
     * @return array Retorna os dados da requisição
     */
    protected function makeTransaction(string $category_id, string $type = "despesa", string $amount = "50.5", string $description = "testDescription"): array
    {
        $transactionController = new TransactionController();
        $transactionController->data = [
            "category_id" => $category_id,
            "type" => $type,
            "amount" => $amount,
            "description" => $description
        ];

        ob_start();
        $transactionController->insert();
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }

    /**
     * Gera um token de autenticação necessário para rotas privadas da aplicação
     * Salva o token gerado no atributo token da classe
     */
    protected function makeToken(string $name = "nameForTestToken", string $email = "emailForTestToken@gmail.com"): void
    {
        $this->makeUser(name: $name, email: $email);
        $authController = new AuthController();
        $authController->data = [
            "email" => $email,
            "password" => "12345678"
        ];

        ob_start();
        $authController->login();
        $response = json_decode(ob_get_clean(), true);

        $this->token = $response['data']['token'];
    }

    /**
     * Este método irá pegar o valor do atributo token da classe e irá gerar os dados com base no token para realizar testes com autenticação necessária
     */
    protected function generateDataAuth(): void
    {
        Auth::validateToken($this->token);
    }
}