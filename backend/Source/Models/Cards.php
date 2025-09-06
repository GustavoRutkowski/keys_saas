<?php

namespace Source\Models;

use Source\Utils\Connect;
use Source\Utils\JWTToken;

class Cards
{
    
    public static function createCard(string $token, array $data) {
        $type = $data['type'] ?? 'debit'; // valor default: 'debit'

        if ($type === 'debit') {
            return self::createDebitCard($token, $data);
        }

        if ($type === 'credit') {
            return self::createCreditCard($token, $data);
        }

    return [ 'success' => false, 'message' => 'invalid card type' ];
}

    // Criar cartão de débito
    private static function createDebitCard(string $token, array $data)
    {
        $jwt = JWTToken::from($token);
        if (!$jwt) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $res = JWTToken::verify($jwt);
        if (!$res['valid']) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $user_id = $res['decoded_token']->id;

        $query = "INSERT INTO debit_cards (nickname, masked_number, cardholder_name, brand, issuer_bank, user_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $data['nickname'], $data['masked_number'], $data['cardholder_name'],
            $data['brand'], $data['issuer_bank'], $user_id
        ];

        $result = Connect::execute($query, $params);

        return isset($result['insertId']) ? [ 'success' => true, 'insertId' => $result['insertId'] ] : [ 'success' => false ];
    }

    // Criar cartão de crédito
    private static function createCreditCard(string $token, array $data)
    {
        
        $jwt = JWTToken::from($token);
        if (!$jwt) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $res = JWTToken::verify($jwt);
        if (!$res['valid']) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $user_id = $res['decoded_token']->id;

        $query = "INSERT INTO credit_cards (nickname, masked_number, cardholder_name, brand, issuer_bank, due_date, user_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['nickname'], $data['masked_number'], $data['cardholder_name'],
            $data['brand'], $data['issuer_bank'], $data['due_date'], $user_id
        ];

        $result = Connect::execute($query, $params);
var_dump($result);
        return isset($result['insertId']) ? [ 'success' => true, 'insertId' => $result['insertId'] ] : [ 'success' => false ];
    }

    // Listar todos os cartões do usuário (débito e crédito)
    public static function getAllCards(string $token)
    {
        $jwt = JWTToken::from($token);
        if (!$jwt) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $res = JWTToken::verify($jwt);
        if (!$res['valid']) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $user_id = $res['decoded_token']->id;

        $debit = Connect::execute("SELECT * FROM debit_cards WHERE user_id = ?", [$user_id])['data'];
        $credit = Connect::execute("SELECT * FROM credit_cards WHERE user_id = ?", [$user_id])['data'];

        return [
            'success' => true,
            'debit_cards' => $debit,
            'credit_cards' => $credit
        ];
    }

    // Deletar cartão de débito
    public static function deleteDebitCard(string $token, int $id)
    {
        return self::deleteCardByType($token, $id, 'debit_cards');
    }

    // Deletar cartão de crédito
    public static function deleteCreditCard(string $token, int $id)
    {
        return self::deleteCardByType($token, $id, 'credit_cards');
    }

    private static function deleteCardByType(string $token, int $id, string $table)
    {
        $jwt = JWTToken::from($token);
        if (!$jwt) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $res = JWTToken::verify($jwt);
        if (!$res['valid']) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $user_id = $res['decoded_token']->id;

        $query = "DELETE FROM {$table} WHERE id = ? AND user_id = ?";
        $result = Connect::execute($query, [$id, $user_id]);

        return (isset($result['affectedRows']) && $result['affectedRows'] > 0)
            ? [ 'success' => true ]
            : [ 'success' => false, 'message' => 'card not found or not owned by user' ];
    }

    // Buscar cartão individual
    public static function getDebitCardById(string $token, int $id)
    {
        return self::getCardById($token, $id, 'debit_cards');
    }

    public static function getCreditCardById(string $token, int $id)
    {
        return self::getCardById($token, $id, 'credit_cards');
    }

    private static function getCardById(string $token, int $id, string $table)
    {
        $jwt = JWTToken::from($token);
        if (!$jwt) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $res = JWTToken::verify($jwt);
        if (!$res['valid']) return [ 'success' => false, 'message' => 'invalid or expired token' ];

        $user_id = $res['decoded_token']->id;

        $query = "SELECT * FROM {$table} WHERE id = ? AND user_id = ?";
        $results = Connect::execute($query, [$id, $user_id])['data'];

        return count($results) === 0
            ? [ 'success' => false, 'message' => 'card not found' ]
            : [ 'success' => true, 'card' => $results[0] ];
    }
}
