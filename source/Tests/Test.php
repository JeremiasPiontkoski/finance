<?php
namespace Source\Tests;

use CoffeeCode\DataLayer\Connect;
use PDO;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = Connect::getInstance();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }
}