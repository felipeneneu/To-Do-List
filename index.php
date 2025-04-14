<?php
require_once("connect.php");
require_once("./classes/Tarefa.php");

$tarefa = new Tarefa($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'salvar' && isset($_POST['titulo'])) {
    $titulo = filter_input(INPUT_POST, 'titulo');
    $descricao = filter_input(INPUT_POST, 'descricao');
    $tarefa->salvar($titulo, $descricao);
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

$tarefas = $tarefa->listar();
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

    <!-- <h1 class="text-3xl font-bold mb-4 text-center text-slate-600">📝 Lista de Tarefas</h1> -->

    <form action="index.php" method="POST" class="flex flex-col gap-4 mb-6">
      <input type="hidden" name="action" value="salvar">
      <input type="text" name="titulo" placeholder="Título" required class="input input-bordered w-full bg-green-200 text-slate-800 placeholder-green-800">
      <textarea name="descricao" placeholder="Descrição" class="textarea textarea-bordered w-full bg-green-200 text-slate-800 placeholder-green-800"></textarea>
      <button type="submit" class="btn w-full bg-green-500 border-none text-slate-200 hover:bg-green-700">Adicionar</button>
    </form>

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




</body>

</html>