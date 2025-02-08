<?php
namespace Source\Tests\Category;

use Source\Controllers\CategoryController;
use Source\Models\Test;
use Source\Support\Auth;

class UpdateCategoryTest extends Test
{
    /**
     * Teste com sucesso
     */
    public function testSuccess(): void
    {
        $insertedCategory = $this->makeCategory();
        $response = $this->updateCategory($insertedCategory['data']['id']);

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
        $response = $this->updateCategory(id: "a");

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
        $response = $this->updateCategory(id: 0);

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
        $insertedCategory = $this->makeCategory();
        $response = $this->updateCategory($insertedCategory['data']['id'], "");

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
        $insertedCategory = $this->makeCategory();
        $response = $this->updateCategory($insertedCategory['data']['id'], $insertedCategory['data']['name']);

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
        $insertedCategory = $this->makeCategory();
        $this->makeToken("nameForTestToken2", "emailForTestToken2@gmail.com");
        $this->generateDataAuth();
        $response = $this->updateCategory($insertedCategory['data']['id']);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(403, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("user", $response['data']);
    }

    /**
     * Método para auxiliar a classe a atualizar dados de uma categoria para teste
     * @param string $id Id da categoria a ser atualizada
     * @param string $name O novo nome da categoria
     * @return array Retorno da requisição de update
     */
    private function updateCategory(string $id, string $name = "nameCategoryForTest2"): array
    {
        $categoryController = new CategoryController();
        $categoryController->data = [
            "name" => $name
        ];

        ob_start();
        $categoryController->update(["id" => $id]);
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }
}