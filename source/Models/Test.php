<?php
namespace Source\Models;


use CoffeeCode\DataLayer\Connect;
use PDO;
use PHPUnit\Framework\TestCase;
use Source\Controllers\AuthController;
use Source\Support\Auth;

class Test extends TestCase
{
    protected PDO $pdo;
    protected string $token;

    protected function setUp(): void
    {
        $this->pdo = Connect::getInstance();
        $this->pdo->beginTransaction();

        $this->makeToken();
        $this->generateDataAuth();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

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

    private function generateDataAuth(): void
    {
        Auth::validateToken($this->token);
    }
}