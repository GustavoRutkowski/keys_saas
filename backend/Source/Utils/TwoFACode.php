<?php

namespace Source\Utils;

require __DIR__ . '/../../vendor/autoload.php';

class TwoFACode {
    public static array $codes = [];

    private int $userId;
    private string $code;
    private int $expiresIn;

    public function __construct(int $userId) {
        define('S', 1000);
        define('MIN', 60 * S);

        $this->userId = $userId;
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

    public static function findByUser(int $userId): array {
        $currentTime = self::getCurrentMilliseconds();
        
        return array_filter(self::$codes, function($twoFACode) use ($userId, $currentTime) {
            return $twoFACode->userId === $userId && $twoFACode->expiresIn > $currentTime;
        });
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
