<?php
namespace Source\Support;


class Auth {
    private static $user = null;

    public static function setUser($user) {
        self::$user = $user;
    }

    public static function getUser() {
        return self::$user;
    }

    public static function check() {
        return self::$user !== null;
    }

    public static function get($key) {
        return self::$user[$key] ?? null;
    }

    public static function requireAuth() {
        if (!self::check()) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized"]);
            exit;
        }
    }

    public static function generateToken(array $data) {
        return JwtToken::create($data);
    }

    public static function validateToken(string $token)
    {
        if (JwtToken::verify($token)) {
            return true;
        }else {
            return false;
        }
    }

    public static function getData()
    {
        return JwtToken::$token->data;
    }
}
