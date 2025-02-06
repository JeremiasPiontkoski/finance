<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class InsertTransactionTest extends Test
{
    /**
     * Teste sucesso
     */
    public function testSuccess(): void
    {
        $transactionController = new TransactionController();
        $transactionController->data = [
            "category_id" => 2,
            "type" => "despesa",
            "amount" => 50.5,
            "description" => "Lanche"
        ];

        ob_start();
        $transactionController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(201, $response['statusCode']);
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
     * Teste com dados vazios
     */
    public function testEmptyData(): void
    {
        $transactionController = new TransactionController();
        $transactionController->data = [];

        ob_start();
        $transactionController->insert();
        $response = json_decode(ob_get_clean(), true);

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
        $transactionController = new TransactionController();
        $transactionController->data = [
            "category_id" => 2,
            "type" => "teste diferente",
            "amount" => 50.5,
            "description" => "Lanche"
        ];

        ob_start();
        $transactionController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("type", $response['data']);
    }

    /**
     * Teste com categoria inválida
     */
    public function testInvalidCategory(): void
    {
        $transactionController = new TransactionController();
        $transactionController->data = [
            "category_id" => 3,
            "type" => "receita",
            "amount" => 50.5,
            "description" => "Lanche"
        ];

        ob_start();
        $transactionController->insert();
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("category_id", $response['data']);
    }
}