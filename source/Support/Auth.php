<?php
namespace Source\Support;

use Source\Models\User;

class Auth {
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
