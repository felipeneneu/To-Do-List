<?php
require_once './vendor/autoload.php';

use app\database\Connection;

// require_once("connect.php");
$pdo = Connection::connect();
require_once("./classes/Tarefa.php");

session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ./app.php");
  exit();
}

$tarefa = new Tarefa($pdo);
$user = $_SESSION['usuario_id'];
$userName = $_SESSION['usuario_nome'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'salvar' && isset($_POST['titulo'])) {
    $titulo = filter_input(INPUT_POST, 'titulo');
    $descricao = filter_input(INPUT_POST, 'descricao');
    $tarefa->salvar($titulo, $descricao, $user);
    header("Location: index.php");
    exit();
  }

  if ($action === 'concluir' && isset($_POST['id'])) {
    $tarefa->concluir($_POST['id']);
    header("Location: index.php");
    exit();
  }

  if ($action === 'deletar' && isset($_POST['id'])) {
    $tarefa->deletar($_POST['id']);
    header("Location: index.php");
    exit();
  }
}

$tarefas = $tarefa->listar($user);
$total = count($tarefas);
$concluidas  = count(array_filter($tarefas, fn($t) => $t['concluido']));
$progresso = $total > 0 ? round(($concluidas / $total) * 100) : 0;

$corBarra = match (true) {
  $progresso <= 30 => 'bg-red-500',
  $progresso <= 70 => 'bg-yellow-400',
  $progresso < 100 => 'bg-blue-500',
  default => 'bg-green-600',
};

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

    <h1 class="text-3xl font-bold mb-4 text-center text-slate-600">📝 Bem vindo: <?= $userName ?></h1>

    <form action="index.php" method="POST" class="flex flex-col gap-4 mb-6">
      <input type="hidden" name="action" value="salvar">
      <input type="text" name="titulo" placeholder="Título" required
        class="input input-bordered w-full bg-green-200 text-slate-800 placeholder-green-800">
      <textarea name="descricao" placeholder="Descrição"
        class="textarea textarea-bordered w-full bg-green-200 text-slate-800 placeholder-green-800"></textarea>
      <button type="submit"
        class="btn w-full bg-green-500 border-none text-slate-200 hover:bg-green-700">Adicionar</button>
    </form>

    <?php if ($total > 0): ?>


      <div class="mb-6">
        <label class="block mb-1 text-sm font-medium text-slate-600">
          Progresso: <?= $progresso ?>%
        </label>
        <div class="w-full bg-green-200 rounded-full h-4 overflow-hidden">
          <div class="<?= $corBarra ?> h-4 transition-all" style="width: <?= $progresso ?>%;"></div>
        </div>
      </div>
    <?php endif; ?>
    <ul class="space-y-4">
      <?php foreach ($tarefas as $t): ?>
        <li class="p-4  bg-green-200 shadow rounded-lg flex justify-between items-center">
          <div>
            <h2 class="text-xl font-semibold text-green-800 <?= $t['concluido'] ? 'line-through text-gray-400' : '' ?>">
              <?= htmlspecialchars($t['titulo']) ?>
            </h2>
            <p class="text-gray-500"><?= htmlspecialchars($t['descricao']) ?></p>
          </div>
          <div class="flex gap-2">
            <?php if (!$t['concluido']): ?>
              <form action="index.php" method="POST">
                <input type="hidden" name="action" value="concluir">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button class="btn btn-success btn-sm">✔</button>
              </form>
            <?php endif; ?>
            <form action="index.php" method="POST">
              <input type="hidden" name="action" value="deletar">
              <input type="hidden" name="id" value="<?= $t['id'] ?>">
              <button class="btn btn-error btn-sm">🗑</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="flex flex-wrap -mx-3 my-5">
    <div class="w-full max-w-full sm:w-3/4 mx-auto text-center">
      <p class="text-sm py-1">
        <a href="./sair.php" class="text-green-900 hover:text-slate-900">Clique para sair.
      </p>
    </div>
  </div>




</body>

</html>