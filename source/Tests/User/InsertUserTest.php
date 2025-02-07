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
        $response = $this->makeUser();
        $this->assertEquals("success", $response['status']);
        $this->assertEquals(201, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
        $this->assertArrayHasKey("name", $response['data']);  
        $this->assertArrayHasKey("email", $response['data']);
    }

    // /**
    //  * Teste dados vazios
    //  */
    public function testEmptyData(): void
    {
        $response = $this->makeUser("", "", "");

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
        $this->assertArrayHasKey("email", $response['data']);
        $this->assertArrayHasKey("password", $response['data']);
    }

    // /**
    //  * Teste email inválido
    //  */
    public function testInvalidEmail(): void
    {
        $response = $this->makeUser(email: "emailForTest");

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
    }

    // /**
    //  * Teste senha inválida
    //  */
    public function testInvalidPassword(): void
    {
        $response = $this->makeUser(password: "12345");

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("password", $response['data']);
    }

    // /**
    //  * Teste email já inserido
    //  */
    public function testEmailAlreadyInserted(): void
    {
        $this->makeUser();
        $response = $this->makeUser();
        
        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("email", $response['data']);
    }
}