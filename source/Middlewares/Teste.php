<?php
namespace Source\Middlewares;

use CoffeeCode\DataLayer\Connect;
use CoffeeCode\Router\Router;
use Source\Models\User;
use Source\Support\Auth;

class Teste {
    public function handle(Router $router): bool
    {
        $users = (new User())->find()->fetch(true);
        foreach($users as $user) {
            echo json_encode(password_verify("12345678", $user->password));
        }

        // $password = password_hash("12345678", PASSWORD_BCRYPT);
        // echo json_encode($password);
        // echo json_encode(password_verify("12345678", $password));
        // echo json_encode(password_verify("12345678", password_hash("12345678", PASSWORD_BCRYPT)));

        return false;

        $token = Auth::generateToken();
        // echo json_encode($token);
        $is_authenticate = Auth::validateToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3Mzc1Njk3ODUsImp0aSI6InFYTjlDTmhtUWtQWHN1TmFZQlJWZ2c9PSIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvcy9maW5hbmNlIiwibmJmIjoxNzM3NTY5Nzg1LCJleHAiOjE3Mzc1Njk4NDUsImRhdGEiOnsidXNlciI6IkplcmVtaWFzIn19.V9eGITUaLgIKRfWcJta7j1ywRkPnSg9iqrIGbhskCv8RocWBrzs-butfMR6Hk9Ghe8RkHl7IeTmdglXFqs67-Q");
        echo json_encode($is_authenticate);
        // echo json_encode(Auth::getData()->user);

        return false;

        // $data = json_decode(file_get_contents("php://input"), true);

        

        // echo json_encode("TO DENTRO DO MIDDLEWARE");
        // var_dump($router->data());

        // echo json_encode($router->data());
        return false;
    }
}