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
        $insertedCategory = $this->makeCategory();
        $response = $this->deleteCategory($insertedCategory['data']['id']);

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
        $response = $this->deleteCategory('a');

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
        $response = $this->deleteCategory(0);

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
        $insertedCategory = $this->makeCategory();
        $this->makeToken("nameForTestToken2", "emailForTestToken2@gmail.com");
        $this->generateDataAuth();
        $response = $this->deleteCategory($insertedCategory['data']['id']);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(403, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("user", $response['data']);
    }

    /**
     * Método para auxiliar a classe a deletar uma categoria
     * @param string $id Id da categoria a ser deletada
     * @return array Retorno da requisição de delete
     */
    private function deleteCategory(string $id): array
    {
        $categoryController = new CategoryController();

        ob_start();
        $categoryController->delete(["id" => $id]);
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }
}