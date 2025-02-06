<?php
namespace Source\Tests\Category;

use Source\Controllers\CategoryController;
use Source\Models\Test;

class DeleteCategoryTest extends Test
{
    /**
     * Teste com sucesso
     */
    public function testSuccess(): void
    {
        $categoryController = new CategoryController();

        ob_start();
        $categoryController->delete(["id" => 2]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }

    /**
     * Teste com id como string('a')
     */
    public function testIdAsString(): void
    {
        $categoryController = new CategoryController();

        ob_start();
        $categoryController->delete(["id" => 'a']);
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

        ob_start();
        $categoryController->delete(["id" => 4]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    /**
     * Teste usuário sem permissão
     */
    public function testPermissionDenied(): void
    {
        $categoryController = new CategoryController();

        ob_start();
        $categoryController->delete(["id" => 3]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(403, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("user", $response['data']);
    }
}