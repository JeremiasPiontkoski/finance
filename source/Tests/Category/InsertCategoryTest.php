<?php
namespace Source\Tests\Category;

use Source\Controllers\CategoryController;
use Source\Models\Test;

class InsertCategoryTest extends Test
{
    public function testSuccess(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => "Conta Internet"
        ];

        ob_start();
        $categoryController->insert();
        $response = json_decode(ob_get_clean(), true);

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

    public function testEmptyData(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [];

        ob_start();
        $categoryController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
    }

    public function testNameAlreadyExists(): void
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => "Conta Água"
        ];

        ob_start();
        $categoryController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("name", $response['data']);
    }
}