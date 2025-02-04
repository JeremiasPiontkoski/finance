<?php
namespace Source\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Finance API", version="1.0")
 * @OA\Server(url="http://localhost/finance")
 */

 /**
 * @OA\SecurityScheme(
 *     securityScheme="TokenJwt",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Login Token"
 * )
*/

class Controller 
{
    public array $data;

    public function __construct()
    {
        $this->data = getDataFromInput();
    }
}