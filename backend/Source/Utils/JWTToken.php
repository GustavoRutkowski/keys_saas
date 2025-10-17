<?php

namespace Source\Utils;

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use \DateTimeImmutable;
use \Exception;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

define('SECRET_KEY', $_ENV['JWT_SECRET']);
define('URL', "http://localhost:{$_ENV['API_PORT']}/backend");

/**
 * JWTToken class:
    * This class is a utility for creating, decoding, and verifying JWT tokens using the Firebase\JWT library.
    * It allows secure authentication and validation of user sessions through signed tokens.
    * Each token contains metadata such as issuance time, expiration, and a custom payload.

 * Setup:
    * To configure the class, simply change the constants above in the 'define' sentence.
    * The constants are: SECRET_KEY and URL.

 * Constructor:
    * Creates a new token with the specified payload and expiration time.

    * @param array $payload -> The data you want to include in the token (e.g., user ID, email, etc.).
    * @param string $expires -> Optional. The expiration time of the token (e.g., '+1 hour', '+1 day'). Default: '+1 hour'.

    * Token structure:
     * - iat: Issued at (timestamp)
     * - jti: Unique token ID
     * - iss: Issuer (the base URL of the API)
     * - nbf: Not before (timestamp)
     * - exp: Expiration timestamp
     * - data: Custom payload

    * @example:
     * # e.g. 1:
     * $token = new JWTToken(['user_id' => 1, 'email' => 'user@example.com']);
     * echo $token->getToken();
     * 
     * # e.g. 2 - Custom expiration:
     * $token = new JWTToken(['user_id' => 2], '+2 days');
     * echo $token->getExpires();

 * from method:
    * Creates a JWTToken object from an existing JWT string.
    * Useful when you receive a token from a client and need to verify or decode it.

    * @param string $token -> The JWT string to be parsed.
    * @return ?JWTToken -> Returns a JWTToken instance if valid, or null if decoding fails.

    * @example:
     * # e.g.:
     * $received = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
     * $tokenObj = JWTToken::from($received);
     * 
     * if ($tokenObj === null) {
     *     echo "Invalid token";
     * } else {
     *     print_r($tokenObj->getPayload());
     * }

 * verify method:
    * Verifies if a token is valid and not expired.

    * @param JWTToken $token -> The JWTToken object to be verified.
    * @return array ->
     * Returns an associative array with the following fields:
     * [
     *     'valid' => bool,           # True if the token is valid
     *     'decoded_token' => array,  # Token payload (if valid)
     *     'message' => string|null   # Error message if invalid
     * ]

    * @example:
     * # e.g.:
     * $tokenObj = JWTToken::from($tokenString);
     * $validation = JWTToken::verify($tokenObj);
     * 
     * if ($validation['valid']) {
     *     echo "Token valid!";
     *     print_r($validation['decoded_token']);
     * } else {
     *     echo "Token invalid: " . $validation['message'];
     * }

 * Getters:
    * getToken() -> Returns the encoded JWT string.
    * getPayload() -> Returns the original payload data.
    * getExpires() -> Returns the expiration date/time of the token.

 * Dependencies:
    * Requires Firebase\JWT.
    * Make sure both are installed via Composer:
     * composer require firebase/php-jwt
 */
class JWTToken {
    private const ALGORITHM = 'HS512';
    private string $value;
    private array $payload;
    private string $expires;

    public function __construct(array $payload, string $expires = '+1 hour') {
        $this->payload = $payload;
        $this->expires = $expires;

        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify($expires)->getTimestamp();

        $data = [
            'iat'  => $issuedAt->getTimestamp(),
            'jti'  => $tokenId,
            'iss'  => URL,
            'nbf'  => $issuedAt->getTimestamp(),
            'exp'  => $expire,
            'data' => $payload
        ];

        $this->value = JWT::encode($data, SECRET_KEY, JWTToken::ALGORITHM);
    }

    // JWTToken::from($token) -> O from gera um objeto do tipo token a partir da string, ja que o verify usa o objeto, n a string
    public static function from(string $token): ?JWTToken {
        try {
            $decoded = JWT::decode($token, new Key(SECRET_KEY, self::ALGORITHM));

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

    public static function verify(JWTToken $token): array {
        try {
            $decoded = JWT::decode($token->getToken(), new Key(SECRET_KEY, JWTToken::ALGORITHM));
            $now = new DateTimeImmutable();

            if (
                $decoded->iss !== URL ||
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

    public function getToken(): string { return $this->value; }
    public function getPayload(): array { return $this->payload; }
    public function getExpires(): string { return $this->expires; }
}
