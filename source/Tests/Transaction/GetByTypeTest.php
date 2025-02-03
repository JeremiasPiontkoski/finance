<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class GetByTypeTest extends Test
{
    public function testDespesaSuccess(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getByType(['type' => "despesa"]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }

    public function testReceitaSuccess(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getByType(['type' => "receita"]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }

    public function testInvalidType(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getByType(['type' => 'teste']);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("type", $response['data']);
    }
}