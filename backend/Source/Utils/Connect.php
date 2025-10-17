<?php

namespace Source\Utils;
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use PDOException;
use InvalidArgumentException;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();


define('CONF_DB_HOST', $_ENV['MYSQL_HOST']); // Ou localhost
define('CONF_DB_NAME', $_ENV['MYSQL_DATABASE']);
define('CONF_DB_USER', $_ENV['MYSQL_USER']);
define('CONF_DB_PASS', $_ENV['MYSQL_PASSWORD']);

/**
 * Connect class:
    * This class is a utility for handling MySQL queries in a database.

 * Setup:
    * To configure the class, simply change the constants above in the 'define' sentence.
    * The constants are: CONF_DB_HOST, CONF_DB_NAME, CONF_DB_USER and CONF_DB_PASS.

 * Connect::execute method:
    * Executes a MySQL query already using prepare for safe parameters.

    * @param string $query -> The MySQL query you want to execute. To enter query parameters, use ? in the string.
    * @param array $data -> 
     * The list of parameters that will replace the ?s in the query.
     * It replaces each ? in the query in the same order as specified, that is, the first item in the array replaces the first ? and so on.
    * @return array -> The parameters of the assoc-array depend on the type of operation:
     * If SELECT, returns an array with the selected data.
     * If INSERT, returns the insertId of operation.
     * If UPDATE, returns nothing.
     * If DELETE, returns how many rows were affected ('affectedRows' field).
     * 
     * Each query also returns an "action" field in the array stating the type of action (DELETE, INSERT, etc.)
     * If any of the operations throw an error, then action = "ERROR" and returns an error message ('message' field).

    * @example:
     * # e.g. 1:
     * $users = Connect::execute('SELECT * FROM users')['data'];
     * 
     * # e.g. 2:
     * $id = $_GET['id'] ?? null;
     * $user = Connect::execute('SELECT * FROM users WHERE id = ?', [$id])['data'][0]; # First user
     * 
     * # e.g. 3:
     * $data = Connect::execute('SELECT * FROM users');
     * echo $data['action']; # Print "SELECT"
 */
class Connect {
    private const OPTIONS = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ];

    private static $instance;
    private static function getInstance(): ?PDO {
        if (empty(self::$instance)) {
            try {
                self::$instance = new PDO(
                    "mysql:host=" . CONF_DB_HOST . ";dbname=" . CONF_DB_NAME,
                    CONF_DB_USER,
                    CONF_DB_PASS,
                    self::OPTIONS
                );
            } catch (PDOException $e) {
                throw $e;
            }
        }

        return self::$instance;
    }

    // Connect::execute("SELECT * FROM users WHERE id = ?", [ $id ]);
    public static function execute(string $query, array $data = []) {
        $queryType = strtoupper(strtok(trim($query), ' '));
        $allowedTypes = ['SELECT', 'INSERT', 'UPDATE', 'DELETE'];

        if (!in_array($queryType, $allowedTypes)) {
            throw new InvalidArgumentException(
                "Tipo de query não suportado: $queryType"
            );
        }

        try {
            $stmt = self::getInstance()->prepare($query);
            $stmt->execute($data);

            return match ($queryType) {
                'INSERT' => [
                    'action' => 'INSERT',
                    'insertId' => self::getInstance()->lastInsertId()
                ],
                'SELECT' => [
                    'action' => 'SELECT',
                    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
                ],
                'UPDATE' => [ 'action' => 'UPDATE' ],
                'DELETE' => [ 'action' => 'DELETE', 'affectedRows' => $stmt->rowCount() ]
            };
        } catch (PDOException $e) {
            return [
                'action' => 'ERROR',
                'message' => $e->getMessage()
            ];
        }
    }
}
