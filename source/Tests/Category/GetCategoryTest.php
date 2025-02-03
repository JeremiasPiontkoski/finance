<?php
namespace Source\Tests\Category;

use Source\Controllers\CategoryController;
use Source\Models\Test;

class GetCategoryTest extends Test
{
    public function testSuccess(): void
    {
        $categoryController = new CategoryController();
        
        ob_start();
        $categoryController->getAllByUser();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }
}