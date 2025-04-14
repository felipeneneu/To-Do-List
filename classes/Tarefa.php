<?php

class Tarefa
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function listar()
  {
    $query = $this->pdo->query("SELECT * FROM tarefas ORDER BY criado_em DESC");
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public function salvar($titulo, $descricao)
  {
    $salvar = $this->pdo->prepare("INSERT INTO tarefas (titulo, descricao, concluido) VALUES (:titulo, :descricao, 0)");
    $salvar->bindValue(":titulo", $titulo);
    $salvar->bindValue(":descricao", $descricao);
    $salvar->execute();
  }

  public function concluir($id)
  {
    $stmt = $this->pdo->prepare("UPDATE tarefas SET concluido = 1 WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
  }

  public function deletar($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM tarefas WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
  }
}
