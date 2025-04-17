<?php

require_once("../../classes/Usuario.php");
require_once("../../classes/Tarefa.php");
define('BASE_PATH', dirname(__DIR__, 2)); // volta duas pastas
require_once BASE_PATH . '/vendor/autoload.php';

use app\database\Connection;
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
  $usuario = $decoded;
  // Tudo certo, retorno os dados do usuário (ou algo que você quiser)
  echo json_encode(['success' => true, 'user' => $decoded]);
} catch (Exception $e) {
  http_response_code(401);
  echo json_encode(['error' => 'Token inválido: ' . $e->getMessage()]);
}
// require_once("../public/auth.php");

// --- FIM DA VERIFICAÇÃO JWT ---
// Se o script chegou até aqui, o usuário está autenticado via JWT!
// $userIdFromJwt e $userNameFromJwt contêm os dados do usuário.

// Mestre Kame diz: Preparando as ferramentas de Tarefa para o Guerreiro autenticado!
$pdo = Connection::connect();
$tarefa = new Tarefa($pdo);

// --- Processamento dos Formulários (Add, Complete, Delete) ---
// Mestre Kame diz: Agora usamos o ID que veio do Scouter (JWT) para as ações!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'salvar' && isset($_POST['titulo'])) {
    $titulo = filter_input(INPUT_POST, 'titulo');
    $descricao = filter_input(INPUT_POST, 'descricao');
    // USA O ID DO JWT AQUI!
    $tarefa->salvar($titulo, $descricao, $usuario->user_id);
    header("Location: index.php"); // Recarrega a página para ver a nova tarefa
    exit();
  }

  if ($action === 'concluir' && isset($_POST['id'])) {
    // Aqui geralmente não precisamos do ID do usuário, só o ID da tarefa,
    // mas seria bom verificar se a tarefa pertence ao usuário $userIdFromJwt antes de concluir.
    // (A classe Tarefa->concluir talvez precise dessa lógica)
    $tarefa->concluir($_POST['id']);
    header("Location: index.php");
    exit();
  }

  if ($action === 'deletar' && isset($_POST['id'])) {
    // Mesma coisa aqui: verificar permissão antes de deletar.
    $tarefa->deletar($_POST['id']);
    header("Location: index.php");
    exit();
  }
}

// --- Busca de Dados ---
// Mestre Kame diz: Listando as missões (tarefas) APENAS deste Guerreiro (identificado pelo JWT)!
$tarefas = $tarefa->listar($usuario->user_id); // USA O ID DO JWT AQUI!
$total = count($tarefas);
$concluidas = count(array_filter($tarefas, fn($t) => $t['concluido']));
$progresso = $total > 0 ? round(($concluidas / $total) * 100) : 0;
// Lógica da cor da barra (continua igual)
$corBarra = match (true) {
  $progresso <= 30 => 'bg-red-500',
  $progresso <= 70 => 'bg-yellow-400',
  $progresso < 100 => 'bg-blue-500',
  default => 'bg-green-600',
};

// Agora o restante do HTML pode usar $userNameFromJwt e $tarefas normalmente
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Lista de Tarefas</title>
  <link href="output.css" rel="stylesheet">
</head>

<body class="bg-green-100 min-h-screen p-8">

  <div class="max-w-xl mx-auto bg-white shadow-lg p-6 rounded-lg">
    <a href="index.php"><img src="./logo.svg" alt="" class="max-w-[200px] mx-auto mb-4"></a>

    <h1 class="text-3xl font-bold mb-4 text-center text-slate-600">📝 Bem vindo:
      <!-- <?= htmlspecialchars($usuario->nome) ?></h1> -->

      <form action="index.php" method="POST" class="flex flex-col gap-4 mb-6">
      </form>

      <?php if ($total > 0): ?>
      <?php endif; ?>
      <ul class="space-y-4">
        <?php foreach ($tarefas as $t): ?>
        <?php endforeach; ?>
      </ul>
  </div>

  <div class="flex flex-wrap -mx-3 my-5">
    <div class="w-full max-w-full sm:w-3/4 mx-auto text-center">
      <p class="text-sm py-1">
        <a href="#" id="logout-link" class="text-green-900 hover:text-slate-900">Clique para sair.</a>
      </p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    const token = sessionStorage.getItem('session');

    if (!token) {
      window.location.href = 'login.html'; // Ou onde estiver sua página de login
    } else {
      fetch('auth.php', {
          method: 'GET',
          headers: {
            'Authorization': 'Bearer ' + token
          }
        })
        .then(response => {
          if (!response.ok) {
            throw new Error("Não autorizado");
          }
          return response.json();
        })
        .then(data => {
          console.log("Usuário autenticado:", data);
          // Você pode usar os dados retornados aqui se quiser
        })
        .catch(error => {
          console.log("Erro de autenticação:", error);
          window.location.href = 'login.php'; // Redireciona de volta se token inválido
        });
    }
  </script>

</body>

</html>