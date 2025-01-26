<?php
namespace Source\Support;

use DateTimeImmutable;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtToken
{
    public static $token = null;

    public static function create (array $dataInfo) : string
    {
        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+10 minutes')->getTimestamp();
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
            "CHAVESECRETA",
            "HS512"
        );
    }

    public static function verify (string $token) : bool
    {
        try {
            $decoded = JWT::decode($token, new Key("CHAVESECRETA", "HS512"));
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