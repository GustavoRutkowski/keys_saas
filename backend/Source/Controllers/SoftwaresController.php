<?php

namespace Source\Controllers;

use Source\Models\Software;

class SoftwaresController extends Controller {

    // POST /softwares
    public function createSoftware(array $data) {
        $body = $this->getRequestData($data)['body'];

        $name = $body['name'] ?? null;
        $icon = $body['icon'] ?? null;

        $created = Software::create($name, $icon);

        if ($created['success'] === false) {
           // return $this::send(400, $created);
           return $this::send(400, [
                'status' => '400',
                'message' => 'software not created!',
                'success' => false,
                'data' => $created
            ]);
        }

        //return $this::send(201, $created);
        return $this::send(201, [
            'status' => '201',
            'message' => 'software created successfully!',
            'success' => true,
            'data' => $created
        ]);
    }

    // GET /softwares
    public function getAllSoftwares(array $data) {
        $results = Software::getAll();

        //var_dump($results);
        //return $this::send(200, ['success' => true, 'data' => $results]);
        return $this::send(200, [
            'status' => '200',
            'message' => 'softwares found!',
            'success' => true,
            'data' => $results
        ]);
    }

    // GET /softwares/{id}
    public function getSoftwareById(array $data) {
        $params = $this->getRequestData($data)['params'];
        $id = $params['id'] ?? null;

        if (!$id) {
            //return $this::send(400, ['success' => false, 'message' => 'missing software id']);
            return $this::send(400, [
                'status' => '400',
                'message' => 'missing software id!',
                'success' => false
            ]);
        }

        $software = Software::getById((int)$id);

        if (isset($software['message'])) {
            //return $this::send(404, $software);
            return $this::send(404, [
                'status' => '404',
                'message' => 'software not found!',
                'success' => false,
                'data' => $software
            ]);
        }

        //return $this::send(200, ['success' => true, 'data' => $software]);
        return $this::send(200, [
            'status' => '200',
            'message' => 'software found!',
            'success' => true,
            'data' => $software
        ]);
    }

    // GET /softwares/pass-id/{passID}
    public function getSoftwareByPasswordId(array $data) {
        $params = $this->getRequestData($data)['params'];
        $passID = $params['passID'] ?? null;

        if (!$passID) {
            //return $this::send(400, ['success' => false, 'message' => 'missing password id']);
            $response = [
                'status' => '400',
                'message' => 'missing password id!',
                'success' => false
            ];
        }

        $software = Software::getByPasswordId((int)$passID);

        if (isset($software['message'])) {
            //return $this::send(404, $software);
            return $this::send(404, [
                'status' => '404',
                'message' => 'Software not found!',
                'success' => false,
                'data' => $software
            ]);
        }

        //return $this::send(200, ['success' => true, 'data' => $software]);
        return $this::send(200, [
            'status' => '200',
            'message' => 'software found!',
            'success' => true,
            'data' => $software
        ]);
    }


    // PUT /softwares/{id}
    public function updateSoftware(array $data) {
        $params = $this->getRequestData($data)['params'];
        $body = $this->getRequestData($data)['body'];

        $id = $params['id'] ?? null;
        $name = $body['name'] ?? null;
        $icon = $body['icon'] ?? null;

        if (!$id) {
            //return $this::send(400, ['success' => false, 'message' => 'missing software id']);
            $response = [
                'status' => '400',
                'message' => 'missing software id!',
                'success' => false
            ];
        }

        $updated = Software::update((int)$id, $name, $icon);

        if (!$updated['success']) {
            //return $this::send(400, $updated);
            return this::send(400, [
                'status' => '400',
                'message' => 'software not updated!',
                'success' => false,
                'data' => $updated
            ]);
        }

        //return $this::send(204, []);
        return $response = [
            'status' => '204',
            'message' => 'software updated successfully!',
            'success' => true,
            'data' => []
        ];
    }

    // DELETE /softwares/{id}
    public function deleteSoftware(array $data) {
        $params = $this->getRequestData($data)['params'];
        $id = $params['id'] ?? null;

        if (!$id) {
            //return $this::send(400, ['success' => false, 'message' => 'missing software id']);
            $response = [
                'status' => '400',
                'message' => 'missing software id!',
                'success' => false
            ];
        }

        $deleted = Software::delete((int)$id);

        if (!$deleted['success']) {
            //return $this::send(404, $deleted);
            return $this::Send(404, [
                'status' => '404',
                'message' => 'software not deleted!',
                'success' => false,
                'data' => $deleted
            ]);
        }

        //return $this::send(204, []);
        return $this::send(204, [
            'status' => '204',
            'message' => 'software deleted successfully!',
            'success' => true,
            'data' => $deleted
        ]);
    }
}
