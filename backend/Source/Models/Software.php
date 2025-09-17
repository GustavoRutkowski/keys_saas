<?php

namespace Source\Models;
use Source\Utils\Connect;
use Source\Utils\ModelException;


class Software extends Model {
    protected static string $TABLE = 'softwares';

    private $id;
    private $name;
    private $icon;

    public function __construct(?int $id, ?string $name = null, ?string $icon = null) {
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
    }

    public static function create(string $name, ?string $icon = null) {
        if (!$name) throw new ModelException('name is required');

        $query = "INSERT INTO softwares (name, icon) VALUES (?, ?)";
        
        $result = Connect::execute($query, [$name, $icon]);
        if (!isset($result['insertId']))
            throw new ModelException('failed to create software');
        
        return $result['insertId'];
    }

    public static function getAll() {
        $query = "SELECT id, name, icon FROM softwares";
        $results = Connect::execute($query)['data'];

        return $results;
    }

    public static function getById(int $id) {
        $query = "SELECT id, name, icon FROM softwares WHERE id = ?";
        $results = Connect::execute($query, [$id])['data'];

        if (count($results) === 0)
            throw new ModelException('software not found');

        return $results[0];
    }

    public static function getByPasswordId(int $passwordId) {
        $query = "
            SELECT s.id, s.name, s.icon
                FROM softwares s
                JOIN passwords p ON p.software_id = s.id
                WHERE p.id = ?
        ";

        $results = Connect::execute($query, [$passwordId])['data'];
        if (count($results) === 0)
            throw new ModelException('software not found for given password');

        return $results[0];
    }

    public static function update(int $id, ?string $name, ?string $icon) {
        $fields = [];
        $params = [];

        if ($name !== null) {
            $fields[] = "name = ?";
            $params[] = $name;
        }

        if ($icon !== null) {
            $fields[] = "icon = ?";
            $params[] = $icon;
        }

        if (empty($fields)) throw new ModelException('no data to update');

        $params[] = $id;

        $query = "UPDATE softwares SET " . implode(', ', $fields) . " WHERE id = ?";
        $result = Connect::execute($query, $params);

        if ((!isset($result['action'])) || $result['action'] !== 'UPDATE')
            throw new ModelException('failed to update');

        return true;
    }

    public static function delete(int $id) {
        $query = "DELETE FROM softwares WHERE id = ?";
        $result = Connect::execute($query, [$id]);

        if (!isset($result['affectedRows']) || $result['affectedRows'] <= 0)
            throw new ModelException('software not found', 404);
        
        return true;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function renameTo(?string $name): void { $this->name = $name; }
    public function getIcon(): ?string { return $this->icon; }
    public function changeIcon(?string $icon): void { $this->icon = $icon; }
}
