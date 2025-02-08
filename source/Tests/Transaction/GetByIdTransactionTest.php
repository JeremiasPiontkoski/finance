<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class GetByIdTransactionTest extends Test
{
    /**
     * Teste com sucesso
     */
    public function testSuccess(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $response = $this->getByIdTransaction($insertedTransaction['data']['id']);

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
        $response = $this->getByIdTransaction('a');

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    /**
     * Teste com id inválido
     */
    public function testInvalidId(): void
    {
        $response = $this->getByIdTransaction(0);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    /**
     * Teste com permissão negada
     */
    public function testPermissionDenied(): void
    {
        $insertedCategory = $this->makeCategory();
        $insertedTransaction = $this->makeTransaction($insertedCategory['data']['id']);
        $this->makeToken("nameForTestToken2", "emailForTestToken2@gmail.com");
        $this->generateDataAuth();
        $response = $this->getByIdTransaction($insertedTransaction['data']['id']);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(403, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("user", $response['data']);
    }

    /**
     * Método para auxiliar a classe a pegar uma transação por id
     * @param string $id Id da transação a ser retornada
     * @return array Retorno da requisição
     */
    private function getByIdTransaction(string $id): array
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getById(["id" => $id]);
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }
}