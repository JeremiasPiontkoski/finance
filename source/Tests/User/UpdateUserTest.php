<?php
namespace Source\Tests\User;

use Source\Controllers\AuthController;
use Source\Controllers\UserController;
use Source\Models\Test;
use Source\Support\Auth;

class UpdateUserTest extends Test
{
    public function testSuccess(): void
    {
        $userController = new UserController();
        $userController->data = [
            "name" => "jeremias",
            "email" => "email2@gmail.com"
        ];

        ob_start();
        $userController->update();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
        $this->assertArrayHasKey("name", $response['data']);
        $this->assertArrayHasKey("email", $response['data']);
    }

    public function testEmptyData(): void
    {
        $userController = new UserController();
        $userController->data = [];

        ob_start();
        $userController->update();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
        $this->assertArrayHasKey("email", $response['data']);
    }

    public function testInvalidEmail(): void
    {
        $userController = new UserController();
        $userController->data = [
            "name" => "jeremias",
            "email" => "jeremias"
        ];

        ob_start();
        $userController->update();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
    }

    public function testOtherUserEmail(): void
    {
        $userController = new UserController();
        $userController->data = [
            "name" => "jeremias",
            "email" => "mateus@gmail.com"
        ];

        ob_start();
        $userController->update();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
    }
}