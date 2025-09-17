<?php

namespace Source\Controllers;

abstract class Controller {
    // $this->getRequestData()["body"]["email"] -> Retorna o email contido no body da requisição
    // $this->getRequestData()["headers"] -> Retorna os headers da requisição

    protected static function getRequestData(): array {
        $headers = function_exists('getallheaders')
            ? getallheaders()
            : Controller::getClientHeadersFallback();

        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($body)) $body = [];

        return [ 'headers' => $headers, 'body' => $body ];
    }

    // self::send(404, 'user not found', false) -> Deixa explicito que deu errado
    // self::send(404, 'user not found') -> Deixa implícito o erro (ele adivinha a partir do status)
    // self::send(200, 'user found successfully', true, $data) -> Envia um objeto $data junto
    // self::send(200, 'user found successfully', data: $data) -> Envia o objeto $data com success implicito (named param)

    protected static function send(int $status, string $message, ?bool $success = null, ?array $data = []) {
        http_response_code($status);

        if ($success === null) {
            // Status maiores que 400 (400, 401, 403, 404, 500) tendem a ser erros
            // Status menores que 400 (200, 201, 204, 300) tendem a ser successo (ou redirect)
            $success = $status < 400;
        }

        header('Content-Type: application/json; charset=UTF-8');

        $response = [
            'status' => $status,
            'success' => $success,
            'message' => $message
        ];

        if ($data !== null) $response['data'] = $data;

        echo json_encode(
            $response,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit();
    }

    private static function getClientHeadersFallback(): array {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[ucwords($name, '-')] = $value;
            }
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }

        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        }

        return $headers;
    }
}