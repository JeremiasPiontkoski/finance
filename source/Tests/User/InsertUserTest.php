<?php
namespace Source\Tests\User;

use Source\Controllers\UserController;
use Source\Models\Test;

class InsertUserTest extends Test
{
    /**
     * Teste sucesso
     */
    public function testSuccess(): void
    {
        $userController = new UserController();
        $userController->data = [
            "name" => "nameForTest",
            "email" => "emailForTest@gmail.com",
            "password" => "12345678"
        ];

        ob_start();
        $userController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(201, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
        $this->assertArrayHasKey("name", $response['data']);  
        $this->assertArrayHasKey("email", $response['data']);
    }

    /**
     * Teste dados vazios
     */
    public function testEmptyData(): void
    {
        $userController = new UserController();
        $userController->data = [];

        ob_start();
        $userController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
        $this->assertArrayHasKey("email", $response['data']);
        $this->assertArrayHasKey("password", $response['data']);
    }

    /**
     * Teste email inválido
     */
    public function testInvalidEmail(): void
    {
        $userController = new UserController();
        $userController->data = [
            "name" => "nameForTest",
            "email" => "emailForTest",
            "password" => "123456"
        ];

        ob_start();
        $userController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
    }

    /**
     * Teste senha inválida
     */
    public function testInvalidPassword(): void
    {
        $userController = new UserController();
        $userController->data = [
            "name" => "nameForTest",
            "email" => "emailForTest@gmail.com",
            "password" => "12345"
        ];

        ob_start();
        $userController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("password", $response['data']);
    }

    /**
     * Teste email já inserido
     */
    public function testEmailAlreadyInserted(): void
    {
        $userController = new UserController();
        $userData = [
            "name" => "nameForTest",
            "email" => "emailForTest@gmail.com",
            "password" => "12345678"
        ];

        $userController->data = $userData;
        ob_start();
        $userController->insert();
        ob_get_clean();

        $userController->data = $userData;
        ob_start();
        $userController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
    }
}