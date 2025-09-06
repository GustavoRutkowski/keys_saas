<?php

namespace Source\Controllers;

use Source\Models\Cards;

class CardsController extends Controller {

    public function createCard(array $data){
        $headers = $this->getRequestData($data)['headers'];
        $body = $this->getRequestData($data)['body'];

        $token = $headers['token'] ?? null;

        $result = Cards::createCard($token, $body);

        if (!$result['success']) {
          //  return $this::send(400, $result);
          return $this::send(400, [
                'status' => '400',
                'message' => 'card not created',
                'success' => false,
                'data' => $result
            ]);
        }
    //return $this::send(201, $result);
    return $this::send(201, [
        'status' => '201',
        'message' => 'card created successfully',
        'success' => true,
        'data' => $result
    ]);
}


    // GET /cards
    public function getAllCards(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $token = $headers['token'] ?? null;

        $result = Cards::getAllCards($token);

        if (!$result['success']) {
           // return $this::send(401, $result);
           return $this::send(401, [
                'status' => '401',
                'message' => 'cards not found',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(200, $result);
        return $this::send(200, [
            'status' => '200',
            'message' => 'found cards!',
            'success' => true,
            'data' => $result
        ]);
    }

    // GET /cards/debit/{id}
    public function getDebitCardById(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $params = $this->getRequestData($data)['params'];

        $token = $headers['token'] ?? null;
        $id = $params['id'] ?? null;

        $result = Cards::getDebitCardById($token, (int)$id);

        if (!$result['success']) {
            //return $this::send(404, $result);
            return $this::send(404, [
                'status' => '404',
                'message' => 'debit card not found!',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(200, $result);
        return $this::send(200, [
            'status' => '200',
            'message' => 'debit card found!',
            'success' => true,
            'data' => $result
        ]);
    }

    // GET /cards/credit/{id}
    public function getCreditCardById(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $params = $this->getRequestData($data)['params'];

        $token = $headers['token'] ?? null;
        $id = $params['id'] ?? null;

        $result = Cards::getCreditCardById($token, (int)$id);

        if (!$result['success']) {
            //return $this::send(404, $result);
            return $this::send(404, [
                'status' => '404',
                'message' => 'credit card not found!',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(200, $result);
        return $this::send(200, [
            'status' => '200',
            'message' => 'credit card found!',
            'success' => true,
            'data' => $result
        ]);
    }

    // DELETE /cards/debit/{id}
    public function deleteDebitCard(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $params = $this->getRequestData($data)['params'];

        $token = $headers['token'] ?? null;
        $id = $params['id'] ?? null;

        $result = Cards::deleteDebitCard($token, (int)$id);

        if (!$result['success']) {
            //return $this::send(404, $result);
            return $this::send(404, [
                'status' => '404',
                'message' => 'debit card not deleted!',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(204, []);
        return $this::send(204, [
            'status' => '204',
            'message' => 'debit card deleted successfully!',
            'success' => true,
            'data' => []
        ]);
    }

    // DELETE /cards/credit/{id}
    public function deleteCreditCard(array $data) {
        $headers = $this->getRequestData($data)['headers'];
        $params = $this->getRequestData($data)['params'];

        $token = $headers['token'] ?? null;
        $id = $params['id'] ?? null;

        $result = Cards::deleteCreditCard($token, (int)$id);

        if (!$result['success']) {
            //return $this::send(404, $result);
            return $this::send(404, [
                'status' => '404',
                'message' => 'credit card not deleted!',
                'success' => false,
                'data' => $result
            ]);
        }

        //return $this::send(204, []);
        return $this::send(204, [
            'status' => '204',
            'message' => 'credit card deleted successfully!',
            'success' => true,
            'data' => []
        ]);
    }
}
