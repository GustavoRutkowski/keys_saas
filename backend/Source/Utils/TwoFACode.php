<?php

namespace Source\Utils;

require __DIR__ . '/../../vendor/autoload.php';

class TwoFACode {
    public static array $codes = [];

    private string $linkedEmail;
    private string $code;
    private int $expiresIn;

    public function __construct(string $linkedEmail) {
        define('S', 1000);
        define('MIN', 60 * S);

        $this->linkedEmail = $linkedEmail;
        $this->code = self::generateCode();
        $this->expiresIn = self::getCurrentMilliseconds() + 10 * MIN;
        
        self::$codes[] = $this;
    }

    private static function generateCode(): string {
        $code = '';

        for ($i = 0; $i < 9; $i++) {
            $digit = (string) rand(0, 9);
            $code .= $digit;
        }

        return $code;
    }

    private static function getCurrentMilliseconds(): int {
        return round(microtime(true) * 1000);
    }

    public static function findCodesByUser(string $email): array {
        $currentTime = self::getCurrentMilliseconds();
        
        return array_filter(self::$codes, function($twoFACode) use ($email, $currentTime) {
            return $twoFACode->linkedEmail === $email && $twoFACode->expiresIn > $currentTime;
        });
    }

    public static function userHasCode(string $email, string $code) {
        $userCodes = self::findCodesByUser($email);
        return in_array($code, $userCodes);
    }

    public function getCode(): string {
        return $this->code;
    }

    public function verify(string $code): bool {
        if ($this->expiresIn <= self::getCurrentMilliseconds())
            return false;
        
        return $this->code === $code;
    }
}
