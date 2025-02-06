<?php
namespace Source\Support;

use DateTimeImmutable;
use Dotenv\Dotenv;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Classe responsável por criar e verificar o TokenJWt
 * @property $token Token de autenticação gerado dentro da classe
 */
class JwtToken
{
    public static $token = null;

    /**
     * Cria o Token de autenticação
     * @param array $dataInfor Dados que serão guardados dentro do token
     * @return string Irá retornar um token jwt para autenticação
     */
    public static function create (array $dataInfo) : string
    {
        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+20 minutes')->getTimestamp();
        $serverName = URL_BASE;

        $data = [
            'iat'  => $issuedAt->getTimestamp(),
            'jti'  => $tokenId,
            'iss'  => $serverName,
            'nbf'  => $issuedAt->getTimestamp(),
            'exp'  => $expire,
            'data' => $dataInfo
        ];

        return JWT::encode(
            $data,
            CONF_API_KEY,
            "HS512"
        );
    }

    /**
     * Verifica se o token é valido ou expirado
     * @param string $token Token a ser verificado
     * @param bool True para caso o token seja válido e False para caso ele não seja válido
     */
    public static function verify (string $token) : bool
    {
        try {
            $decoded = JWT::decode($token, new Key(CONF_API_KEY, "HS512"));
            self::$token = $decoded;

            $now = new DateTimeImmutable();
            $serverName = URL_BASE;
            if ($decoded->iss !== $serverName || $decoded->nbf > $now->getTimestamp() || $decoded->exp < $now->getTimestamp()) {
                return false;
            }
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}