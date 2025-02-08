<?php
namespace Source\Tests\Category;

use Source\Controllers\CategoryController;
use Source\Models\Test;

class InsertCategoryTest extends Test
{
    /**
     * Teste com sucesso
     */
    public function testSuccess(): void
    {
        $response = $this->makeCategory();

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(201, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
        $this->assertArrayHasKey("user_id", $response['data']);
        $this->assertArrayHasKey("name", $response['data']);
        $this->assertArrayHasKey("created_at", $response['data']);
        $this->assertArrayHasKey("updated_at", $response['data']);
    }

    /**
     * Teste sem dados
     */
    public function testEmptyData(): void
    {
        $response = $this->makeCategory("");

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
        $this->makeCategory();
        $response = $this->makeCategory();

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
    }
}