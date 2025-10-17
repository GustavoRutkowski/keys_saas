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

/**
 * EmailSender class:
    * This class is a utility for sending emails using PHPMailer with SMTP configuration.
    * It loads credentials from environment variables and allows sending HTML or plain text messages
    * to one or multiple recipients.

 * Setup:
    * To configure the class, simply change the constants above in the 'define' sentence.
    * The constants are: SENDER_EMAIL and SENDER_PASSWORD.

    * The class automatically configures PHPMailer with Gmail's SMTP server.
    * Default settings:
     * Host: smtp.gmail.com
     * Port: 587
     * Encryption: STARTTLS
     * Authentication: Enabled

 * Constructor:
    * @param array $users ->
     * List of recipients to send the email to.
     * Each item must be an associative array containing:
     * [
     *     "email" => "recipient@example.com",
     *     "name" => "Recipient Name" # Optional
     * ]

    * @example:
     * # e.g. 1 - Single recipient:
     * $email = new EmailSender([
     *     ["email" => "user@example.com", "name" => "John Doe"]
     * ]);
     *
     * # e.g. 2 - Multiple recipients:
     * $email = new EmailSender([
     *     ["email" => "user1@example.com", "name" => "Alice"],
     *     ["email" => "user2@example.com", "name" => "Bob"]
     * ]);

 * send method:
    * Sends an email to all previously defined recipients.
    * Supports both HTML and plain text (alternative) content.

    * @param string $title -> The subject of the email.
    * @param string $content -> The main content of the email in HTML format.
    * @param string|null $altBody -> Optional. Plain text version of the message (for clients that don't support HTML).

    * @return bool -> Returns true if the email was successfully sent, or false if sending failed.

    * @example:
     * # e.g.:
     * $email = new EmailSender([
     *     ["email" => "recipient@example.com", "name" => "Jane Doe"]
     * ]);
     *
     * $sent = $email->send(
     *     "Welcome to Keys!",
     *     "<h1>Hello Jane!</h1><p>Your registration was successful.</p>",
     *     "Hello Jane! Your registration was successful."
     * );
     *
     * if ($sent) {
     *     echo "Email sent successfully!";
     * } else {
     *     echo "Failed to send email.";
     * }

 * Dependencies:
    * Requires PHPMailer.
    * Make sure both are installed via Composer:
     * composer require phpmailer/phpmailer
 */
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
            if (!User::emailExists($user['email'])) continue;
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
