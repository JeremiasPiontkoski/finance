<?php
namespace Source\Support;

class Response
{
    /**
     * Retorna uma resposta de sucesso.
     *
     * @param string $message Mensagem de sucesso.
     * @param int $statusCode Código de status HTTP (padrão: 200).
     * @param mixed $response Dados opcionais a serem incluídos.
     * @return void
     */
    public static function success(string $message, int $statusCode = 200, $response = null): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'success',
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $response,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna uma resposta de erro genérico.
     *
     * @param string $message Mensagem de erro.
     * @param int $statusCode Código de status HTTP (padrão: 400).
     * @param mixed $response Dados opcionais a serem incluídos.
     * @return void
     */
    public static function error(string $message, int $statusCode = 400, $response = null): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'error',
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $response,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function notFound(string $message = "Nenhum resultado encontrado!", $response = null): void
    {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'statusCode' => 404,
            'message' => $message,
            'data' => $response,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna uma resposta de erro de servidor.
     *
     * @param mixed $response Dados opcionais a serem incluídos.
     * @return void
     */
    public static function serverError($response = null): void
    {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'statusCode' => 500,
            'message' => "Erro interno no servidor, tente novamente!",
            'data' => $response
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna uma resposta para token inválido ou expirado.
     *
     * @param mixed $response Dados opcionais a serem incluídos.
     * @return void
     */
    public static function invalidToken($response = null): void
    {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'statusCode' => 401,
            'message' => "Token inválido ou expirado!",
            'data' => $response,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }


    /**
     * Retorna uma resposta para dados de login inválidos
     *
     * @param mixed $response Dados opcionais a serem incluídos.
     * @return void
     */
    public static function invalidLogin($response = null): void
    {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'statusCode' => 401,
            'message' => "Email e/ou senha inválidos!",
            'data' => $response,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna uma resposta para usuário sem permissão.
     *
     * @param mixed $response Dados opcionais a serem incluídos.
     * @return void
     */
    public static function forbidden($response = null): void
    {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'statusCode' => 403,
            'message' => "Você não tem permissão para acessar esta rota!",
            'data' => $response,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
