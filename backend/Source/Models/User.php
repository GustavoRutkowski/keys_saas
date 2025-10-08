<?php

namespace Source\Models;

use Source\Utils\Connect;
use Source\Utils\JWTToken;
use Exception;
use Source\Utils\ModelException;
use Source\Models\Model;
use Source\Utils\FileUploader;
use Source\Utils\EmailSender;

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

    public static function emailExists(string $email): bool {
        $query = 'SELECT COUNT(*) AS count FROM users WHERE email = ?';
        $firstRow = Connect::execute($query, [$email])['data'][0];

        return $firstRow['count'] > 0;
    }

    public static function create($name, $email, $main_pass) {
        if (!$name || !$email || !$main_pass)
            throw new ModelException('name, email and main_pass are required!');

        if (self::emailExists($email))
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

    public static function getByEmail(string $email) {
        if (!$email) throw new ModelException('email is reqired');

        $query = 'SELECT id, name, email, picture FROM users WHERE email = ?';

        $results = Connect::execute($query, [ $email ])['data'];
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
                2 * KB, 5 * GB,
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
                throw new ModelException('failed to upload image', 500, previous: $e);
            }
        }

        $updateQuery .= join(',', array_map(fn($e) => (string)$e . ' = ?', array_keys($updateParams))) . ' WHERE id = ?';
        $params = array_values($updateParams);

        $result = Connect::execute($updateQuery, [...$params, $id]);
        if ($result['action'] !== 'UPDATE')
            throw new ModelException('failed to update user', 500);
        
        return true;
    }

    public static function changePassword(
        string $token,
        string $main_pass,
        string $new_main_pass,
        string $repeat_new_main_pass
    ) {
        $id = self::authenticate($token);
        
        $results = Connect::execute('SELECT main_pass FROM users WHERE id = ?', [$id])['data'];
        $user = $results[0];

        $passwordIsValid = password_verify($main_pass, $user['main_pass']);
        if (!$passwordIsValid)
            throw new ModelException('invalid main_pass!', 403);

        if ($new_main_pass !== $repeat_new_main_pass)
            throw new ModelException('passwords do not match');

        if (strlen($new_main_pass) < 8)
            throw new ModelException('password must be 8 or more characters long');

        $encoded_pass = password_hash($new_main_pass, PASSWORD_DEFAULT);
        $result = Connect::execute('UPDATE users SET main_pass = ? WHERE id = ?', [$encoded_pass, $id]);
        if ($result['action'] !== 'UPDATE')
            throw new ModelException('failed to change user main_pass', 500);
        
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
            throw new ModelException('invalid attempt', 401);

        $token = new JWTToken([ 'id' => $user['id'], 'email' => $user['email'] ]);
        return $token->getToken();
    }

    public static function recoverAccount(string $email): bool {
        $user = self::getByEmail($email);
        
        $recoverToken = new JWTToken(
            ['id' => $user['id'], 'email' => $user['email']],
            '+10 minutes'
        );

        $recoverAccountURL = "http://localhost:4321/reset_password?token={$recoverToken->getToken()}";
        
        $title = "Keys - Recover your Account";
        $content = "
            <h2>Hi, {$user['name']}!</h2>
            <p>You've requested account recovery. Click the link below to change your password.</p>
            <a href=$recoverAccountURL>Reset Password</a>
        ";

        $sender = new EmailSender([$user]);
        $success = $sender->send($title, $content);

        if (!$success) throw new ModelException('failed to send message to user', 500);
        return true;
    }

    public static function resetPassword(
        string $recover_token,
        string $new_main_pass,
        string $repeat_new_main_pass
    ): bool {
        $id = self::authenticate($recover_token);
        
        if ($new_main_pass !== $repeat_new_main_pass)
            throw new ModelException('passwords do not match');

        if (strlen($new_main_pass) < 10)
            throw new ModelException('password must be 10 or more characters long');

        $encoded_pass = password_hash($new_main_pass, PASSWORD_DEFAULT);

        $query = 'UPDATE users SET main_pass = ? WHERE id = ?';
        $result = Connect::execute($query, [$encoded_pass, $id]);
        if ($result['action'] !== 'UPDATE')
            throw new ModelException('failed to reset user main_pass', 500);
        
        return true;
    }

    public static function send2FACode(string $email): void {
        // Envia um email com um código de 9 digitos para o usuário
        // Retorna true ou um erro


    }

    public static function verify2FACode(string $email, string $code) {
        // Verifica se o código informado existe para o usuário (pegar o id por email).
        // Caso sim, pega os dados guardados (no Redis) e cadastra um user no banco com esses dados.
    }

    // Getters & Setters:
    
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function getEmail(): ?string { return $this->email; }
    public function getPicture(): ?string { return $this->picture; }
    public function setName(?string $name): void { $this->name = $name; }
    public function changePicture(?string $picture): void { $this->picture = $picture; }
}