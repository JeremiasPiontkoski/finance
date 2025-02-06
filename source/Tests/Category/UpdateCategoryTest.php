<?php
namespace Source\Tests\Category;

use Source\Controllers\CategoryController;
use Source\Models\Test;

class UpdateCategoryTest extends Test
{
    /**
     * Teste com sucesso
     */
    public function testSuccess(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => "Conta Internet"
        ];

        ob_start();
        $categoryController->update(["id" => 2]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
        $this->assertArrayHasKey("user_id", $response['data']);
        $this->assertArrayHasKey("name", $response['data']);
        $this->assertArrayHasKey("created_at", $response['data']);
        $this->assertArrayHasKey("updated_at", $response['data']);
    }

    /**
     * Teste com id como string('a')
     */
    public function testIdAsString(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => "Conta Internet"
        ];

        ob_start();
        $categoryController->update(["id" => 'a']);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }
    
    /**
     * Teste com id inexistente
     */
    public function testInvalidId(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => "Conta Internet"
        ];

        ob_start();
        $categoryController->update(["id" => 4]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    /**
     * Teste sem dados
     */
    public function testEmptyData(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [];

        ob_start();
        $categoryController->update(["id" => 2]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
    }

    /**
     * Teste com nome existente
     */
    public function testNameAlreadyExists(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => "Conta Água"
        ];

        ob_start();
        $categoryController->update(["id" => 2]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
    }

    /**
     * Teste com permissão negada
     */
    public function testPermissionDenied(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => "Conta Água"
        ];

        ob_start();
        $categoryController->update(["id" => 3]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(403, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("user", $response['data']);
    }
}