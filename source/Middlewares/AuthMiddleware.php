<?php
namespace Source\Middlewares;

use Dotenv\Repository\RepositoryInterface;
use Source\Support\Auth;
use Source\Support\Response;

class AuthMiddleware
{
    public function handle()
    {
        $headers = getallheaders();

        if (empty($headers['Authorization'])) {
            Response::invalidToken();
            return false;
        }

        $authorizationHeader = getallheaders()['Authorization'];
        $token = str_replace("Bearer ", "", $authorizationHeader);

        if (!Auth::validateToken($token)) {
            Response::invalidToken();
            return false;
        }

        return true;
    }
}