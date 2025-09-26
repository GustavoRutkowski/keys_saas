<?php

namespace Source\Controllers;

use Source\Models\Password;
use Source\Utils\ModelException;

class PasswordsController extends Controller {
    public static function createPassword() {
        $headers = self::getRequestData()['headers'];
        $body = self::getRequestData()['body'];

        $token = $headers['token'] ?? null;
        $value = $body['value'] ?? null;
        $software_id = $body['software_id'] ?? null;

        try {
            $created = Password::create($token, $value, $software_id);

            $res = [ 'insertId' => $created ];
            self::send(201, 'password created successfully', data: $res);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function getAllPasswords() {
        $token = self::getRequestData()['headers']['token'];  

        try {
            $results = Password::getAllByUser($token);

            self::send(200, 'passwords found successfully', data: $results);
        } catch (ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function getPasswordById(int $id) {
        $token = self::getRequestData()['headers']['token'] ?? null;

        try {
            $result = Password::getById($token, (int)$id);
            self::send(200, 'password found successfully', data: $result);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function updatePassword(int $id) {
        $token = self::getRequestData()['headers']['token'] ?? null;
        $body = self::getRequestData()['body'];

        $value = $body['value'] ?? null;
        $software_id = $body['software_id'] ?? null;

        try {
            Password::update($token, (int)$id, $value, $software_id);
            self::send(200, 'password updated successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function deletePassword(int $id) {
        $token = self::getRequestData()['headers']['token'] ?? null;
        
        try {
            $result = Password::delete($token, (int)$id);
            self::send(200, 'password deleted successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function sudoModeStart() {
        $token = self::getRequestData()['headers']['token'] ?? null;
        $main_pass = self::getRequestData()['body'] ?? null;

        try {
            $sudo_token = Password::sudoStart($token, $main_pass);
            self::send(200, 'sudo mode started successfully', data: [ 'sudo_token' => $sudo_token ]);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }
}
