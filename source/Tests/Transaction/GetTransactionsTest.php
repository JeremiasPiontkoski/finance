<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class GetTransactionsTest extends Test
{
    /**
     * Teste sucesso
     */
    public function testSuccess(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getAll();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }
}