<?php
namespace Source\Models;


use CoffeeCode\DataLayer\Connect;
use PDO;
use PHPUnit\Framework\TestCase;
use Source\Controllers\AuthController;
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
     * Gera um token de autenticação necessário para rotas privadas da aplicação
     * Salva o token gerado no atributo token da classe
     */
    private function makeToken(): void
    {
        $authController = new AuthController();
        $authController->data = [
            "email" => "jeremias@gmail.com",
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
    private function generateDataAuth(): void
    {
        Auth::validateToken($this->token);
    }
}