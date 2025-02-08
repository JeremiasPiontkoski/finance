<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class DeleteTransactionTest extends Test
{
    /**
     * Teste com sucesso
     */
    public function testSuccess(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->deleteTransaction($insertedTransaction['data']['id']);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }

    /**
     * Teste id como string('a')
     */
    public function testIdAsString(): void
    {
        $response = $this->deleteTransaction('a');

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
        $response = $this->deleteTransaction(0);

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
        $response = $this->deleteTransaction($insertedTransaction['data']['id']);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(403, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("user", $response['data']);
    }

    /**
     * Método para auxiliar a classe a deletar uma transação
     * @param string $id Id da transação a ser deletada
     * @return array Retorno da requisição de delete
     */
    private function deleteTransaction(string $id): array
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->delete(["id" => $id]);
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }
}