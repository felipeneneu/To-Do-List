<?php
require_once __DIR__ . '/../vendor/autoload.php';

use app\database\Connection;
use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$email = filter_input(INPUT_POST, 'email');
$senha = filter_input(INPUT_POST, 'senha');

// echo json_encode($email);

$pdo = Connection::connect();
$auth = $pdo->prepare("SELECT * FROM usuario WHERE email = :email AND ativo = 1");
$auth->execute([
  'email' => $email
]);

$user = $auth->fetch();

if (!$user) {
  http_response_code(401);
}

if (!password_verify($senha, $user->senha)) {
  http_response_code(401);
}

$payload = [
  "exp" => time() + 2600,
  "iat" => time(),
  "email" => $email
];

$encode = JWT::encode($payload, $_ENV['KEY'], 'HS256');
echo json_encode($encode);


// echo json_encode('teste');