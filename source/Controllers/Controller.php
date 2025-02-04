<?php
namespace Source\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Finance: Api gerenciadora de gastos e receitas", version="1.0")
 * @OA\Server(url="http://localhost/finance/api.php")
 */

class Controller 
{
    public array $data;

    public function __construct()
    {
        $this->data = getDataFromInput();
    }
}