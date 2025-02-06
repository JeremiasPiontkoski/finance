<?php
namespace Source\Middlewares;

use Source\Support\Auth;
use Source\Support\Response;

/**
 * Classe responsável pela proteção de rotas privadas
 */
class AuthMiddleware
{
    /**
     * Verifica se o token é valido
     * @return bool True caso o token seja válido e False caso não seja
     */
    public function handle(): bool
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