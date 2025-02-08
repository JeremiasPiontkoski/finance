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
        $insertedCategory = $this->makeCategory();
        $response = $this->makeTransaction($insertedCategory['data']['id']);

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
        $response = $this->makeTransaction(0, "", "", "", "");

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
        $response = $this->makeTransaction($insertedCategory['data']['id'], type: "testType");

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
        $response = $this->makeTransaction(0);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("category_id", $response['data']);
    }
}