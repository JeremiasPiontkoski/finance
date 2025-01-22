<?php
namespace Source\Support;

class Response
{
    private static function call (int $code, string $type): array
    {
        http_response_code($code);
        return [
            "code" => $code,
            "type" => $type
        ];
    }
}