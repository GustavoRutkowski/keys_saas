<?php

ob_start();

require  __DIR__ . "/vendor/autoload.php";

// Sempre retorna headers básicos
$allowedOrigin = '*';
// if (isset($_SERVER['HTTP_ORIGIN'])) $allowedOrigin = $_SERVER['HTTP_ORIGIN'];

header("Access-Control-Allow-Origin: " . $allowedOrigin);
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, token");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use Bramus\Router\Router;
use Source\Controllers\UsersController;
use Source\Controllers\PasswordsController;
use Source\Controllers\SoftwaresController;
use Source\Controllers\CardsController;
// const API_HOST = "https://localhost:{$apiPort}/backend";


/* ------------------------------ ROUTES ------------------------------ */

$router = new Router();

// Users
$router->mount('/users', function() use ($router) {
    $router->post('/', fn() => UsersController::createUser());

    $router->get('/id/{id}', fn($id) => UsersController::getUserByID($id));
    $router->get('/user', fn() => UsersController::getUser());

    $router->put('/user', fn() => UsersController::updateUser());
    $router->put('/user/main_pass', fn() => UsersController::updateUserMainPass());

    $router->post('/login', fn() => UsersController::login());
});

// Passwords
$router->mount('/passwords', function() use ($router) {
    $router->post('/create', fn() => PasswordsController::createPassword());
    
    $router->get('/all', fn() => PasswordsController::getAllPasswords());
    $router->get('/id/{id}', fn($id) => PasswordsController::getPasswordById($id));
    
    $router->put('/update/{id}', fn($id) => PasswordsController::updatePassword($id));

    $router->delete('/delete/{id}', fn($id) => PasswordsController::deletePassword($id));
    
    $router->post('/sudo', fn() => PasswordsController::sudoModeStart());
});

// Softwares
$router->mount('/softwares', function() use ($router) {
    $router->post('/', fn() => SoftwaresController::createSoftware());

    $router->get('/all', fn() => SoftwaresController::getAllSoftwares());
    $router->get('/id/{id}', fn($id) => SoftwaresController::getSoftwareById($id));
    $router->get('/pass-id/{passID}', fn($passID) => SoftwaresController::getSoftwareByPasswordId($passID));

    $router->put('/id/{id}', fn($id) => SoftwaresController::updateSoftware($id));

    $router->delete('/id/{id}', fn($id) => SoftwaresController::deleteSoftware($id));

});

// Cards
$router->mount('/cards', function() use ($router) {
    $router->post('/', fn() => CardsController::createCard());

    $router->get('/all', fn() => CardsController::getAllCards());

    $router->get('/debit/{id}', fn($id) => CardsController::getDebitCardById($id));
    $router->get('/credit/{id}', fn($id) => CardsController::getCreditCardById($id));

    $router->delete('/debit/{id}', fn($id) => CardsController::deleteDebitCard($id));
    $router->delete('/credit/{id}', fn($id) => CardsController::deleteCreditCard($id));

});

// Qualquer erro levará para esse caminho:

$router->set404(function() {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "code" => 404,
        "status" => "not_found",
        "message" => "URL não encontrada"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

$router->run();

ob_end_flush();