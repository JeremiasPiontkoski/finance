<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class GetByTypeTransactionTest extends Test
{
    /**
     * Teste sucesso com despesa
     */
    public function testDespesaSuccess(): void
    {
        $response = $this->getByTypeTransactions("despesa");

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }

    /**
     * Teste sucesso com receita
     */
    public function testReceitaSuccess(): void
    {
        $response = $this->getByTypeTransactions("receita");

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }

    /**
     * Teste tipo inválido
     */
    public function testInvalidType(): void
    {
        $response = $this->getByTypeTransactions("testType");

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("type", $response['data']);
    }

    /**
     * Método para auxiliar a classe a pegar uma transação por type
     * @param string $type Type da transação a ser retornada
     * @return array Retorno da requisição
     */
    private function getByTypeTransactions(string $type): array
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getByType(["type" => $type]);
        $response = json_decode(ob_get_clean(), true);
        return $response;
    }
}