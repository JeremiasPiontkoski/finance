 <?php

use CoffeeCode\Router\Router;
use Source\Middlewares\Teste;

ini_set('display_errors', 1);
header('Content-Type: application/json; charset=UTF-8');



require  __DIR__ . "/vendor/autoload.php";

$router = new Router(URL_BASE);
$router->namespace("Source\Controllers");

$router->group("users");
$router->post("/", "UserController:insert");

$router->group("auth");
$router->post("/", "AuthController:login");

$router->dispatch();

// /** ERROR REDIRECT */
if ($router->error()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "errors" => [
            "type " => "endpoint_not_found",
            "message" => "Não foi possível processar a requisição"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}