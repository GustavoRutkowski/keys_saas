<?php

namespace Source\Controllers;

use Source\Models\Cards;
use Source\Utils\ModelException;

class CardsController extends Controller {

    public static function createCard(){
        $token = self::getRequestData()['headers']['token'] ?? null;
        $body = self::getRequestData()['body'];

        try {
            $insertId = Cards::createCard($token, $body);

            $res = [ 'insertId' => $insertId ];
            self::send(201, 'card created successfully', data: $res);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function getAllCards() {
        $token = self::getRequestData()['headers']['token'] ?? null;

        try {
            $result = Cards::getAllCards($token);
            self::send(200, 'cards found successfully', data: $result);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function getDebitCardById(int $id) {
        $token = self::getRequestData()['headers']['token'] ?? null;

        try {
            $result = Cards::getDebitCardById($token, $id);
            self::send(200, 'debit card found', data: $result);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    // GET /cards/credit/{id}
    public static function getCreditCardById(int $id) {
        $token = self::getRequestData()['headers']['token'] ?? null;

        try {
            $result = Cards::getCreditCardById($token, $id);
            self::send(200, 'debit card found', data: $result);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function deleteDebitCard(int $id) {
        $token = self::getRequestData()['headers']['token'] ?? null;

        try {
            Cards::deleteDebitCard($token, $id);
            self::send(204, 'debit card deleted successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function deleteCreditCard(int $id) {
        $token = self::getRequestData()['headers']['token'] ?? null;

        try {
            Cards::deleteCreditCard($token, $id);
            self::send(204, 'debit card deleted successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }
}
