<?php
namespace Source\Support;

/**
 * Classe com métodos de autenticação
 */
class Auth {
    /**
     * Gera um token de autenticação
     * @param array $data Dados que serão criptografados no token
     * @return string Irá retornar o token
     */
    public static function generateToken(array $data): string
    {
        return JwtToken::create($data);
    }

    /**
     * Valida se o token é valido ou expirado
     * @param string $token Token a ser verificado
     * @return bool True caso o token seja válido e False caso não seja válido
     */
    public static function validateToken(string $token): bool
    {
        if (JwtToken::verify($token)) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * Retorna um objeto com os dados do token
     * @return object Dados do token
     */
    public static function getData(): object
    {
        return JwtToken::$token->data;
    }
}
