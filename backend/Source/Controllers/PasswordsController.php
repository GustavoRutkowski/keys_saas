<?php

namespace Source\Controllers;

use Source\Models\Password;

class PasswordsController extends Controller {

    // POST /passwords
    public function createPassword(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $body = $this->getRequestData($data)['body'];

        $token = $headers['token'] ?? null;
        $value = $body['value'] ?? null;
        $software_id = $body['software_id'] ?? null;

        $created = Password::create($token, $value, $software_id);

        if ($created['success'] === false) {
            //return $this::send(400, $created);
            return $this::send(400, [
                'status' => '400',
                'message' => 'password not created!',
                'success' => false,
                'data' => $created
            ]);
        }

        //return $this::send(201, $created);
        return $this::send(201, [
        'status' => '201',
        'message' => 'password created sucessfully',
        'success' => true,
        'data' => $created
        ]);
    }

    // GET /password
    public function getAllPasswords(array $data)
{
    $token = $this->getRequestData($data)['headers']['token'];  

    $results = Password::getAllByUser($token);

    
    if (isset($results['success']) && $results['success'] === false) {
        // Token inválido ou erro
        //return $this::send(401, $results);
        return $this::send(401, [
        'status' => '401',
        'message' => 'invalid token or error',
        'success' => false,
        'data' => $results
        ]);
    }
    
    var_dump($results); 

    if (is_array($results) && count($results) === 0) {
       return $this::send(404, [
        'status' => '404',
        'message' => 'password not found',
        'success' => false,
        'data' => $results
        ]);
        //return $this::send(404, [ 'success' => false, 'message' => 'password not found' ]);
    }

    return $this::send(200, [
        'status' => '200',
        'message' => 'passowords found',
        'success' => true,
        'data' => $results
    ]);
     //$this::send(200, [ 'success' => true, 'data' => $results ]);
}



    // GET /passwords/{id}
    public function getPasswordById(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $params = $this->getRequestData($data)['params'];

        $token = $headers['token'] ?? null;
        $id = $params['id'] ?? null;

        $result = Password::getById($token, (int)$id);

        if (isset($result['success']) && $result['success'] === false) {
            //return $this::send(404, $result);
            return $this::send(404, [
                'status' => '404',
                'message' => 'password not found',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(200, $result);
        return $this::send(200, [
            'status' => '200',
            'message' => 'password found',
            'success' => true,
            'data' => $result
        ]);
    }

    // PUT /passwords/{id}
    public function updatePassword(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $body = $this->getRequestData($data)['body'];
        $params = $this->getRequestData($data)['params'];

        $token = $headers['token'] ?? null;
        $id = $params['id'] ?? null;

        $value = $body['value'] ?? null;
        $software_id = $body['software_id'] ?? null;

        $result = Password::update($token, (int)$id, $value, $software_id);

        if (isset($result['success']) && !$result['success']) {
            //return $this::send(400, $result);
            return $this::send(400, [
                'status' => '400',
                'message' => 'password not updated',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(204, []);
        return $this::send(204, [
            'status' => '204',
            'message' => 'updated sucessfully',
            'success' => true,
            'data' => []
        ]);
    }

    // DELETE /passwords/{id}
    public function deletePassword(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $params = $this->getRequestData($data)['params'];

        $token = $headers['token'] ?? null;
        $id = $params['id'] ?? null;

        $result = Password::delete($token, (int)$id);

        if (isset($result['success']) && !$result['success']) {
            //return $this::send(404, $result);
            return $this::send(404, [
                'status' => '404',
                'message' => 'password not deleted',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(204, []);
        return $this::send(204, [
            'status' => '204',
            'message' => 'password deleted',
            'success' => true,
            'data' => []
        ]);
    }
}
