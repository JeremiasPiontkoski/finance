<?php
namespace Source\Tests\Transaction;

use Source\Controllers\TransactionController;
use Source\Models\Test;

class GetByIdTransactionTest extends Test
{
    public function testSuccess(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getById(['id' => 1]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("success", $response['status']);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
    }

    public function testIdAsString(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getById(['id' => 'a']);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    public function testInvalidId(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getById(['id' => 2]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(400, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("id", $response['data']);
    }

    public function testPermissionDenied(): void
    {
        $transactionController = new TransactionController();

        ob_start();
        $transactionController->getById(['id' => 6]);
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals("error", $response['status']);
        $this->assertEquals(403, $response['statusCode']);
        $this->assertArrayHasKey("message", $response);
        $this->assertArrayHasKey("data", $response);
        $this->assertArrayHasKey("user", $response['data']);
    }
}