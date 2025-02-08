<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class UpdateTransactionTest extends Test
{
    /**
     * Teste sucesso
     */
    public function testSuccess(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->updateTransaction($insertedTransaction['data']['id'], $insertedTransaction['data']['category_id']);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
        $this->assertArrayHasKey("user_id", $response['data']);
        $this->assertArrayHasKey("category_id", $response['data']);
        $this->assertArrayHasKey("type", $response['data']);
        $this->assertArrayHasKey("amount", $response['data']);
        $this->assertArrayHasKey("description", $response['data']);
        $this->assertArrayHasKey("created_at", $response['data']);
        $this->assertArrayHasKey("updated_at", $response['data']);
    }

    /**
     * Teste dados vazios
     */
    public function testEmptyData(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->updateTransaction($insertedTransaction['data']['id'], null, "", "", "");

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("category_id", $response['data']);
        $this->assertArrayHasKey("type", $response['data']);
        $this->assertArrayHasKey("amount", $response['data']);
    }

    /**
     * Teste tipo inválido
     */
    public function testInvalidType(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->updateTransaction($insertedTransaction['data']['id'], $insertedTransaction['data']['category_id'], "testType");

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("type", $response['data']);
    }

    /**
     * Teste categoria inválida
     */
    public function testInvalidCategory(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->updateTransaction($insertedTransaction['data']['id'], 0);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("category_id", $response['data']);
    }

    /**
     * Teste id como string('a')
     */
    public function testIdAsString(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->updateTransaction("a", $insertedTransaction['data']['id']);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    /**
     * Teste id inválido
     */
    public function testInvalidId(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->updateTransaction(0, $insertedTransaction['data']['id']);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    /**
     * Teste permissão negada
     */
    public function testPermissionDenied(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $this->makeToken("nameForTestToken2", "emailForTestToken2@gmail.com");
        $this->generateDataAuth();
        $response = $this->updateTransaction($insertedTransaction['data']['id'], $insertedTransaction['data']['id']);
        
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
    private function updateTransaction(string $id, string|null $category_id, string $type = "despesa", string $amount = "50.5", string $description = "testDescription"): array
    {
        $transactionController = new TransactionController();
        $transactionController->data = [
            "category_id" => $category_id,
            "type" => $type,
            "amount" => $amount,
            "description" => $description
        ];

        ob_start();
        $transactionController->update(['id' => $id]);
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }
}