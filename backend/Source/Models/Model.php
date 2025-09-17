<?php

namespace Source\Models;
use Source\Utils\JWTToken;
use Source\Utils\ModelException;

abstract class Model {
    protected static ?string $TABLE = null;

    protected static function authenticate($token) {
        if (!$token) throw new ModelException('token is required');

        $jwt = JWTToken::from($token);
        if ($jwt === null)
            throw new ModelException('expired or invalid token!', 401);

        $res = JWTToken::verify($jwt);
        if (!$res['valid'])
            throw new ModelException('expired or invalid token!', 401);

        $id = $res['decoded_token']->id;
        return $id;
    }
}