<?php
require_once("connect.php");
require_once("./classes/Usuario.php");
require_once("./classes/Tarefa.php");


header("Content-Type: application/json");

$user = new Usuario($pdo);

$headers = getallheaders();
$token = $headers['Authorization'] ?? '';

if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
  $token = $matches[1];
  $usuario = $user->verificarToken($token);

  var_dump($usuario);

  if ($usuario) {
    echo json_encode(["mensagem" => "Bem-vindo, {$usuario->nome}"]);
    // Aqui carrega seus dados do sistema
  } else {
    http_response_code(401);
    echo json_encode(['erro' => 'Token inválido']);
  }
} else {
  http_response_code(401);
  echo json_encode(['erro' => 'Token não enviado']);
}

// --- FIM DA VERIFICAÇÃO JWT ---
// Se o script chegou até aqui, o usuário está autenticado via JWT!
// $userIdFromJwt e $userNameFromJwt contêm os dados do usuário.

// Mestre Kame diz: Preparando as ferramentas de Tarefa para o Guerreiro autenticado!

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

  <script>
    // Mestre Kame diz: Ensinando o Guerreiro a descartar o Scouter (logout)
    const token = localStorage.getItem('token');

    fetch('index.php', {
        headers: {
          'Authorization': 'Bearer ' + token
        }
      })
      .then(res => res.json())
      .then(dados => {
        if (dados.erro) {
          alert('Login expirado. Faça login novamente.');
          window.location.href = 'login.html';
        } else {
          console.log(dados.mensagem);
        }
      });
  </script>

</body>

</html>