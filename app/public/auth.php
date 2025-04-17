<?php
define('BASE_PATH', dirname(__DIR__, 2)); // volta duas pastas
require_once BASE_PATH . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With");
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Captura o token do header Authorization
$headers = getallheaders();
$authorization = $headers['Authorization'] ?? null;

if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
  http_response_code(401);
  echo json_encode(['error' => 'Token não fornecido']);
  exit;
}

$token = str_replace('Bearer ', '', $authorization);

try {
  $decoded = JWT::decode($token, new Key($_ENV['KEY'], 'HS256'));
  // Tudo certo, retorno os dados do usuário (ou algo que você quiser)
  echo json_encode(['success' => true, 'user' => $decoded]);
} catch (Exception $e) {
  http_response_code(401);
  echo json_encode(['error' => 'Token inválido: ' . $e->getMessage()]);
}

// $headers = apache_request_headers();

// $authorization = $headers['Authorization'];
// $token = str_replace('Bearer ', '', $authorization);

// echo json_encode($token);

// $authorization = $_SERVER["HTTP_AUTHORIZATION"];

// echo json_encode($authorization);