<?php
namespace Source\Tests\Auth;

use Source\Controllers\AuthController;
use Source\Tests\Test;

class AuthTest extends Test
{

    public function testSuccess(): void
    {
        $authController = new AuthController();
        $authController->data = [
            "email" => "jeremias@gmail.com",
            "password" => "12345678"
        ];

        ob_start();
        $authController->login();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
        $this->assertArrayHasKey("token", $response['data']);
    }

    public function testEmptyData(): void
    {
        $authController = new AuthController();
        $authController->data = [];

        ob_start();
        $authController->login();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
        $this->assertArrayHasKey("password", $response['data']);
    }

    public function testInvalidEmail(): void
    {
        $authController = new AuthController();
        $authController->data = [
            "email" => "jeremias",
            "password" => "12345678"
        ];

        ob_start();
        $authController->login();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
    }

    public function testInvalidData(): void
    {
        $authController = new AuthController();
        $authController->data = [
            "email" => "jeremias2@gmail.com",
            "password" => "12345678"
        ];

        ob_start();
        $authController->login();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(401, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }
}