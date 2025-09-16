<?php

namespace Source\Utils;

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use \DateTimeImmutable;
use \Exception;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class JWTToken
{
    private static string $secretKey = $_ENV['JWT_SECRET'];
    private static string $url = "http://localhost:{$_ENV('JWT_SECRET')}";
    private const ALGORITHM = 'HS512';
    private string $value;
    private array $payload;
    private string $expires;

    public function __construct(array $payload, string $expires = '+1 hour')
    {
        $this->payload = $payload;
        $this->expires = $expires;

        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify($expires)->getTimestamp();

        $data = [
            'iat'  => $issuedAt->getTimestamp(),
            'jti'  => $tokenId,
            'iss'  => JWTToken::$url,
            'nbf'  => $issuedAt->getTimestamp(),
            'exp'  => $expire,
            'data' => $payload
        ];

        $this->value = JWT::encode($data, JWTToken::$secretKey, JWTToken::ALGORITHM);
    }

    // JWTToken::from($token) -> O from gera um objeto do tipo token a partir da string, ja que o verify usa o objeto, n a string
    public static function from(string $token): ?JWTToken
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, self::ALGORITHM));

            $instance = new self([]);

            $instance->value = $token;
            $instance->payload = (array)($decoded->data ?? []);

            $expireTimestamp = $decoded->exp ?? null;

            if ($expireTimestamp) {
                $expireDate = (new DateTimeImmutable())->setTimestamp($expireTimestamp);
                $instance->expires = $expireDate->format('Y-m-d H:i:s');
                
                return $instance;
            }

            $instance->expires = '';
            return $instance;
        } catch (Exception) {
            return null;
        }
    }


    public static function verify(JWTToken $token): array
    {
        try {
            $decoded = JWT::decode($token->getToken(), new Key(JWTToken::$secretKey, JWTToken::ALGORITHM));
            $now = new DateTimeImmutable();

            if (
                $decoded->iss !== JWTToken::$url ||
                $decoded->nbf > $now->getTimestamp() ||
                $decoded->exp < $now->getTimestamp()
            ) {
                return [
                    'valid' => false,
                    'message' => 'token expired or invalid',
                    'decoded_token' => null
                ];
            }

            return [
                'valid' => true,
                'decoded_token' => $decoded->data,
                'message' => null
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'decoded_token' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getToken(): string
    {
        return $this->value;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getExpires(): string
    {
        return $this->expires;
    }
}
