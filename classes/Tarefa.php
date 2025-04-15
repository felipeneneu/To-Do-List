<?php

class Tarefa
{
  // Aqui é onde vamos se conectar com banco de dados
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }
  // Aqui começa os metodos que nosso objeto consegue fazer
  // listar = acessa o banco e puxa todas as informaçoes da tabela
  public function listar($usuario_id)
  {
    $query = $this->pdo->prepare("SELECT * FROM tarefas WHERE usuario_id = :usuario_id ORDER BY criado_em DESC");
    $query->bindValue(':usuario_id', $usuario_id);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  // Aqui é a logica de salvar no banco de dados! recebemos o titulo e a descricao do nosso form(Frontend) 
  public function salvar($titulo, $descricao, $usuario_id)
  {
    $salvar = $this->pdo->prepare("INSERT INTO tarefas (titulo, descricao, concluido, usuario_id) VALUES (:titulo, :descricao, 0, :usuario_id)");
    $salvar->bindValue(":titulo", $titulo);
    $salvar->bindValue(":descricao", $descricao);
    $salvar->bindValue(":usuario_id", $usuario_id);
    $salvar->execute();
  }
  // Aqui é o metodo que podemos concluir uma atividade
  public function concluir($id)
  {
    $stmt = $this->pdo->prepare("UPDATE tarefas SET concluido = 1 WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
  }

  // Aqui é facil ne? serve para deletar
  public function deletar($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM tarefas WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
  }
}
