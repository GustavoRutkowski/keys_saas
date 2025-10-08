<?php

namespace Source\Utils;

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;
use Source\Models\User;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

define('SENDER_EMAIL', $_ENV['MAIL_SENDER_EMAIL']);
define('SENDER_PASSWORD', $_ENV['MAIL_SENDER_PASSWORD']);

class EmailSender {
    private static ?PHPMailer $mailerInstance = null;

    private static function setup(): void {
        if (self::$mailerInstance === null) {
            self::$mailerInstance = new PHPMailer(true);

            self::$mailerInstance->isSMTP();
            self::$mailerInstance->Host = "smtp.gmail.com";
            self::$mailerInstance->SMTPAuth = true;
            self::$mailerInstance->Username = SENDER_EMAIL;
            self::$mailerInstance->Password = SENDER_PASSWORD;
            self::$mailerInstance->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            self::$mailerInstance->Port = 587;

            self::$mailerInstance->setFrom(SENDER_EMAIL, 'Keys');
        }
    }

    public function __construct(array $users = []) {
        self::setup();
        
        foreach ($users as $user) {
            if (User::emailExists($user['email'])) continue;
            self::$mailerInstance->addAddress($user['email'], $user['name'] ?? null);
        }
    }

    public function send(string $title, string $content, ?string $altBody = null): bool {
        self::$mailerInstance->isHTML(true);

        self::$mailerInstance->Subject = $title;
        self::$mailerInstance->Body    = $content;

        if ($altBody !== null) {
            self::$mailerInstance->AltBody = $altBody;
        }

        try {
            self::$mailerInstance->send();
            return true;
        } catch(MailerException $e) {
            return false;
        }
    }
}
