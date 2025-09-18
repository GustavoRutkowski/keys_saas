<?php

namespace Source\Controllers;
use Source\Models\User;
use Source\Utils\ModelException;

class UsersController extends Controller {
    public static function createUser() {
        $body = self::getRequestData()['body'];

        $insertId = User::create(
            $body['name'],
            $body['email'],
            $body['main_pass']
        );

        $res = [ 'insertId' => $insertId ];
        self::send(201, 'user created successfully!', data: $res);
    }

    public static function getUserByID(int $id) {
        try {
            $user = User::getById($id);
            self::send(200, 'user found successfully', data: $user);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function getUser() {
        $token = self::getRequestData()['headers']['token'];
        
        try {
            $user = User::getByToken($token);
            self::send(200, 'user found successfully', data: $user);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function updateUser() {
        $token = self::getRequestData()['headers']['token'];
        $body = self::getRequestData()['body'];

        try {
            User::updateInfos(
                $token,
                $body['name'] ?? null,
                $body['picture'] ?? null
            );

            self::send(204, 'user updated successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function updateUserMainPass() {
        $token = self::getRequestData()['headers']['token'];
        $body = self::getRequestData()['body'];

        try {
            User::changePassword(
                $token, 
                $body['main_pass'],
                $body['new_main_pass'],
                $body['repeat_new_main_pass']
            );

            self::send(204, 'main_pass changed successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function login() {
        $body = self::getRequestData()['body'];

        try {
            $token = User::login($body['email'], $body['main_pass']);
            self::send(200, 'user logged in successfully', data: [ 'token' => $token ]);
        } catch (ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }
}