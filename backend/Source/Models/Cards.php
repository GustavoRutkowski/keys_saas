<?php

namespace Source\Models;

use Source\Utils\Connect;
use Source\Utils\JWTToken;
use Source\Utils\ModelException;

class Cards extends Model {
    public static function createCard(string $token, array $data) {
        $type = $data['type'] ?? 'debit'; // valor default: 'debit'

        if ($type === 'debit') return self::createDebitCard($token, $data);
        if ($type === 'credit') return self::createCreditCard($token, $data);

        // Fallback
        throw new ModelException('invalid card type. Use debit or credit');
    }

    private static function createDebitCard(string $token, array $data) {
        $user_id = self::authenticate($token);

        $query = "INSERT INTO debit_cards (nickname, masked_number, cardholder_name, brand, issuer_bank, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $data['nickname'], $data['masked_number'], $data['cardholder_name'],
            $data['brand'], $data['issuer_bank'], $user_id
        ];

        $result = Connect::execute($query, $params);
        if (!isset($result['insertId']))
            throw new ModelException('failed to create debit card', 500);

        return $result['insertId'];
    }

    private static function createCreditCard(string $token, array $data) {
        $user_id = self::authenticate($token);

        $query = "INSERT INTO credit_cards (nickname, masked_number, cardholder_name, brand, issuer_bank, due_date, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['nickname'], $data['masked_number'], $data['cardholder_name'],
            $data['brand'], $data['issuer_bank'], $data['due_date'], $user_id
        ];

        $result = Connect::execute($query, $params);
        if (!isset($result['insertId']))
            throw new ModelException('failed to create credit card', 500);

        return $result['insertId'];
    }

    public static function getAllCards(string $token) {
        $user_id = self::authenticate($token);

        $debit = Connect::execute("SELECT * FROM debit_cards WHERE user_id = ?", [$user_id])['data'];
        $credit = Connect::execute("SELECT * FROM credit_cards WHERE user_id = ?", [$user_id])['data'];

        return [ 'debit_cards' => $debit, 'credit_cards' => $credit ];
    }

    public static function deleteDebitCard(string $token, int $id) {
        return self::deleteCardByType($token, $id, 'debit_cards');
    }

    public static function deleteCreditCard(string $token, int $id) {
        return self::deleteCardByType($token, $id, 'credit_cards');
    }

    private static function deleteCardByType(string $token, int $id, string $table) {
        $user_id = self::authenticate($token);

        $query = "DELETE FROM {$table} WHERE id = ? AND user_id = ?";
        $result = Connect::execute($query, [$id, $user_id]);

        if ((!isset($result['affectedRows']) || $result['affectedRows'] > 0))
            throw new ModelException('card not found or not owned by user', 404);

        return true;
    }

    public static function getDebitCardById(string $token, int $id) {
        return self::getCardById($token, $id, 'debit_cards');
    }

    public static function getCreditCardById(string $token, int $id) {
        return self::getCardById($token, $id, 'credit_cards');
    }

    private static function getCardById(string $token, int $id, string $table) {
        $user_id = self::authenticate($token);

        $query = "SELECT * FROM {$table} WHERE id = ? AND user_id = ?";
        $results = Connect::execute($query, [$id, $user_id])['data'];

        if (count($results) === 0)
            throw new ModelException('card not found', 404);

        return $results[0];
    }
}
