<?php

namespace Source\Models;

use Source\Utils\Connect;
use Exception;
use Source\Models\Model;
use Source\Utils\ModelException;

class Password extends Model
{
    protected static ?string $TABLE = 'passwords';

    private $id;
    private $value;
    private $user_id;
    private $software_id;

    public function __construct(
        ?int $id,
        ?string $value = null,
        ?int $user_id = null,
        ?int $software_id = null
    )
    {
        $this->id = $id;
        $this->value = $value;
        $this->user_id = $user_id;
        $this->software_id = $software_id;
    }

    public static function create(string $token, string $value, ?int $software_id = null) {
        $user_id = self::authenticate($token);

        $query = "INSERT INTO passwords (value, user_id, software_id) VALUES (?, ?, ?)";
        $createdPassword = Connect::execute($query, [$value, $user_id, $software_id]);

        try {
            return $createdPassword['insertId'];
        } catch (Exception $e) {
            throw new ModelException('failed to create password', 500, previous: $e);
        }
    }

    public static function getAllByUser(string $token)
    {
        $user_id = self::authenticate($token);

        $query = "SELECT id, value, software_id FROM passwords WHERE user_id = ?";
        $results = Connect::execute($query, [$user_id])['data'];

        return $results;
    }

    public static function getById(string $token, int $id)
    {
        $user_id = self::authenticate($token);

        $query = "SELECT id, value, software_id FROM passwords WHERE id = ? AND user_id = ?";
        
        $results = Connect::execute($query, [$id, $user_id])['data'];
        if (count($results) === 0)
            throw new ModelException('password not found', 404);

        return $results[0];
    }

    public static function update(string $token, int $id, ?string $value, ?int $software_id)
    {
        $user_id = self::authenticate($token);

        $fields = [];
        $params = [];

        if ($value !== null) { $fields[] = 'value = ?'; $params[] = $value; }
        if ($software_id !== null) { $fields[] = 'software_id = ?'; $params[] = $software_id; }

        if (empty($fields))
            throw new ModelException('no data to update');

        $params[] = $id;
        $params[] = $user_id;

        $query = "UPDATE passwords SET " . implode(', ', $fields) . " WHERE id = ? AND user_id = ?";
        
        $result = Connect::execute($query, $params);
        if (!isset($result['action']) || $result['action'] !== 'UPDATE')
            throw new ModelException('failed to update', 500);

        return true;
    }

    public static function delete(string $token, int $id)
    {
        $user_id = self::authenticate($token);

        $query = "DELETE FROM passwords WHERE id = ? AND user_id = ?";

        $result = Connect::execute($query, [$id, $user_id]);
        if (!isset($result['affectedRows']) || $result['affectedRows'] <= 0)
            throw new ModelException('password not found or not owned by user', 404);
        
        return true;
    }

    public function getId(): ?int { return $this->id; }
    public function changePassword(?string $value): void { $this->value = $value; }
    public function getSoftwareId(): ?int { return $this->software_id; }
    public function changeSoftware(?int $software_id): void { $this->software_id = $software_id; }
}
