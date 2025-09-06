<?php

// PUT /users/user

namespace Source\Controllers;

use Source\Models\User;

class UsersController extends Controller {
    // POST
    public function createUser(array $data) {
        $body = $this->getRequestData($data)['body'];

        $createdUser = User::create(
            $body['name'],
            $body['email'],
            $body['main_pass']
        );

        $this::send(201, [
            'status' => '201',
            'message' => 'user created successfully!',
            'success' => true,
            'data' => $createdUser
        ]);
    }

    // GET
    public function getUserByID(array $data) {
        //var_dump($data);

        $id = $this->getRequestData($data)['params']['id'];

        $user = User::getById($id);

        if (array_key_exists('message', $user)) return $this::send(404, $user);
        $this::send(200, $user);
    }
    public function getUser(array $data) {
        $token = $this->getRequestData($data)['headers']['token'];

        $user = User::getByToken($token);

        if (array_key_exists('message', $user)) return $this::send(404, $user);
        $this::send(200, $user);
    }

    // PUT
    public function updateUser(array $data) {
        $token = $this->getRequestData($data)['headers']['token'];
        $body = $this->getRequestData($data)['body'];

        $userUpdated = User::update(
            $token,
            $body['name'],
            $body['main_pass'],
            $body['picture']
        );

        if ($userUpdated['success']) $this::send(204, [
        'status' => '204',
        'message' => 'user not updated!',
        'success' => false,
        ]);
        
        //$this::send(400, $userUpdated);
        $this::send(400, [
        'status' => '400',
        'message' => 'user not updated!',
        'success' => false,
        'data' => $userUpdated
        ]);
    }

    // POST LOGIN
    public function login(array $data = []) {
        $body = $this->getRequestData($data)['body'];

        $loginResult = User::login($body['email'], $body['main_pass']);

        return $loginResult['success']
            ? $this::send(200, $loginResult)
            : $this::send(401, $loginResult);
    }
}