<?php

namespace Source\Controllers;

use Source\Models\Software;
use Source\Utils\ModelException;

class SoftwaresController extends Controller {
    public static function createSoftware() {
        $body = self::getRequestData()['body'];

        $name = $body['name'] ?? null;
        $icon = $body['icon'] ?? null;

        try {
            $insertId = Software::create($name, $icon);
    
            $res = [ 'insertId' => $insertId ];
            self::send(201, 'software created successfully', data: $res);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function getAllSoftwares() {
        $results = Software::getAll();
        self::send(200, 'softwares found successfully', data: $results);
    }

    public static function getSoftwareById(int $id) {
        try {
            $software = Software::getById((int)$id);
            self::send(200, 'software found successfully', data: $software);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function getSoftwareByPasswordId(int $passID) {
        try {
            $software = Software::getByPasswordId($passID);
            self::send(200, 'software found successfully', data: $software);
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function updateSoftware(int $id) {
        $body = self::getRequestData()['body'];

        $name = $body['name'] ?? null;
        $icon = $body['icon'] ?? null;

        try {
            Software::update((int)$id, $name, $icon);
            self::send(204, 'software updated successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }

    public static function deleteSoftware(int $id) {
        try {
            Software::delete((int)$id);
            self::send(204, 'software deleted successfully');
        } catch(ModelException $e) {
            self::send($e->getHttpStatus(), $e->getMessage());
        }
    }
}
