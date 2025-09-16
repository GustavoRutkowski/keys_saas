<?php

ob_start();

require  __DIR__ . "/vendor/autoload.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Access-Control-Allow-Credentials: true'); // Permitir credenciais

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use Bramus\Router\Router;
use Source\Controllers\UsersController;

// const API_HOST = "https://localhost:{$apiPort}/backend";


/* ------------------------------ ROUTES ------------------------------ */

$router = new Router();
$namespace = "Source\\Controllers\\";

$callController = function(string $controllerMethod, array $params = []) use ($namespace) {
    [$controller, $method] = explode(':', $controllerMethod);

    $class = $namespace . $controller;

    if (!class_exists($class)) {
        throw new \Exception("Classe $class não encontrada!");
    }

    $instance = new $class();

    return $instance->$method($params);
};

/* USERS */

$router->mount('/users', function() use ($router) {
    $router->post('/', fn() => UsersController::createUser());

    $router->get('/id/{id}', fn($id) => UsersController::getUserByID($id));
    $router->get('/user', fn() => UsersController::getUser());

    $router->put('/user', fn() => UsersController::updateUser());

    $router->post('/login', fn() => UsersController::login());
});

$router->mount('/passwords', function() use ($router, $callController) {
    $router->post('/create', fn() => $callController('PasswordsController:createPassword'));
    
    $router->get('/all', fn() => $callController('PasswordsController:getAllPasswords'));
    $router->get('/id/{id}', fn($id) => $callController('PasswordsController:getPasswordById', ['id'=> $id]));
    
    $router->put('/update/{id}', fn($id) => $callController('PasswordsController:updatePassword', ['id'=> $id]));

    $router->delete('/delete/{id}', fn($id) => $callController('PasswordsController:deletePassword', ['id'=> $id]));
});

$router->mount('/softwares', function() use ($router, $callController) {
    $router->post('/', fn() => $callController('SoftwaresController:createSoftware'));

    $router->get('/all', fn() => $callController('SoftwaresController:getAllSoftwares'));
    $router->get('/id/{id}', fn($id) => $callController('SoftwaresController:getSoftwareById', ['id'=> $id]));
    $router->get('/pass-id/{passID}', fn($passID) => $callController('SoftwaresController:getSoftwareByPasswordId', ['id'=> $passID]));

    $router->put('/id/{id}', fn($id) => $callController('SoftwaresController:updateSoftware', ['id'=> $id]));

    $router->delete('/id/{id}', fn($id) => $callController('SoftwaresController:deleteSoftware', ['id'=> $id]));

});

$router->mount('/cards', function() use ($router, $callController) {
    $router->post('/', fn() => $callController('CardsController:createCard'));

    $router->get('/all', fn() => $callController('CardsController:getAllCards'));

    $router->get('/debit/{id}', fn($id) => $callController('CardsController:getDebitCardById', ['id'=> $id]));
    $router->get('/credit/{id}', fn($id) => $callController('CardsController:getCreditCardById', ['id'=> $id]));

    $router->delete('/debit/{id}', fn($id) => $callController('CardsController:deleteDebitCard', ['id'=> $id]));
    $router->delete('/credit/{id}', fn($id) => $callController('CardsController:deleteCreditCard', ['id'=> $id]));

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