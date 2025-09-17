<?php

namespace Source\Models;

use Source\Utils\Connect;
use Source\Utils\JWTToken;
use Exception;
use Source\Utils\ModelException;
use Source\Models\Model;
use Source\Utils\FileUploader;

class User extends Model {
    protected static ?string $TABLE = 'users';

    private $id;
    private $name;
    private $email;
    private $password;
    private $picture;

    public function __construct(
        ?int $id,
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
        ?string $photo = null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->picture = $photo;

        User::create($this->name, $this->email, $this->password);
    }

    public static function create($name, $email, $main_pass) {
        if (!$name || !$email || !$main_pass)
            throw new ModelException('name, email and main_pass are required!');

        $countQuery = 'SELECT COUNT(*) AS count FROM users WHERE email = ?';
        $firstRow = Connect::execute($countQuery, [$email])['data'][0];

        if ($firstRow['count'] > 0)
            throw new ModelException('email already exists');

        $query = 'INSERT INTO users (name, email, main_pass) VALUES (?, ?, ?)';
        $hashedPassword = password_hash($main_pass, PASSWORD_DEFAULT);

        $createdUser = Connect::execute($query, [$name, $email, $hashedPassword]);
        return $createdUser['insertId'];
    }

    public static function getByToken(string $token) {
        $id = self::authenticate($token);
        return User::getById($id);
    }

    public static function getById(int $id) {
        if (!$id) throw new ModelException('id is reqired');

        $query = 'SELECT id, name, email, picture FROM users WHERE id = ?';

        $results = Connect::execute($query, [ $id ])['data'];
        if (count($results) === 0)
            throw new ModelException('user not found', 404);

        $user = $results[0];
        return $user;
    }

    public static function updateInfos(string $token, ?string $name, ?array $picture) {
        $id = self::authenticate($token);

        $updateQuery = 'UPDATE users SET ';
        $updateParams = [];

        if ($name) $updateParams['name'] = $name;

        // Se passar a foto de maneira inválida:
        if ($picture && (!isset($picture['filename']) || !isset($picture['data'])))
            throw new ModelException("picture attribute must contain 'filename' and 'data'");

        if ($picture) {
            define('KB', 1024);
            define('GB', 1024 * KB);

            $uploader = new FileUploader(
                2 * KB,
                5 * GB,
                ['jpg', 'jpeg', 'png', 'gif'],
                'img_', # Prefixo dos arquivos salvos
                '../../../frontend/public/imgs/upload' # Pasta para salvar
            );

            $user = User::getById($id);
            
            // Remove a foto antiga, caso exista
            if ($user['picture'] !== null) $uploader->removeFile($user['picture']);

            try {
                $filename = $uploader->uploadFile($picture);
                $updateParams['picture'] = $filename;
            } catch(Exception $e) {
                var_dump($e->getMessage());
                throw new ModelException('failed to upload image: ' . $e->getMessage(), 500, previous: $e);
            }
        }

        $updateQuery .= join(',', array_map(fn($e) => (string)$e . ' = ?', array_keys($updateParams)));
        $params = array_values($updateParams);

        var_dump($updateQuery);

        $result = Connect::execute($updateQuery, $params);
        if ($result['action'] !== 'UPDATE')
            throw new ModelException('failed to update user', 500);
        
        return true;
    }

    public static function login($email, $main_pass) {
        if (!$email || !$main_pass)
            throw new ModelException('email and main_pass are required!');

        $query = 'SELECT * FROM users WHERE email = ?';
        
        $results = Connect::execute($query, [$email])['data'];
        $userNotFound = count($results) === 0;

        if ($userNotFound)
            throw new ModelException('invalid attempt');
        
        $user = $results[0];

        $passwordIsValid = password_verify($main_pass, $user['main_pass']);

        if (!$passwordIsValid)
            throw new ModelException('invalid attempt', 403);

        $token = new JWTToken([ 'id' => $user['id'], 'email' => $user['email'] ]);
        return $token->getToken();
    }

    // Getters & Setters:
    
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function getEmail(): ?string { return $this->email; }
    public function getPicture(): ?string { return $this->picture; }
    public function setName(?string $name): void { $this->name = $name; }
    public function changePassword(?string $password): void { $this->password = $password; }
    public function changePicture(?string $picture): void { $this->picture = $picture; }
}