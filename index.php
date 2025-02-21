 <?php

use CoffeeCode\Router\Router;
use Source\Middlewares\AuthMiddleware;
use Source\Support\Response;

ini_set('display_errors', 1);
header('Content-Type: application/json; charset=UTF-8');

require  __DIR__ . "/vendor/autoload.php";

$router = new Router(URL_BASE);
$router->namespace("Source\Controllers");

$router->group("users");
$router->post("/", "UserController:insert");
$router->put("/", "UserController:update", middleware: AuthMiddleware::class);

$router->group("auth");
$router->post("/", "AuthController:login");

$router->group("categories", middleware: AuthMiddleware::class);
$router->post("/", "CategoryController:insert");
$router->get("/", "CategoryController:getAllByUser");
$router->put("/{id}", "CategoryController:update");
$router->delete("/{id}", "CategoryController:delete");

$router->group("transactions", middleware: AuthMiddleware::class);
$router->post("/", "TransactionController:insert");
$router->put("/{id}", "TransactionController:update");
$router->delete("/{id}", "TransactionController:delete");
$router->get("/{id}", "TransactionController:getById");
$router->get("/", "TransactionController:getAll");
$router->get("/type/{type}", "TransactionController:getByType");

$router->group("files", middleware: AuthMiddleware::class);
$router->get("/csv/{type}", "FileController:exportByTypeToCsv");

$router->dispatch();

// /** ERROR REDIRECT */
if ($router->error()) {
    Response::endpoint_not_found();
}