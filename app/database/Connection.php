<?php

namespace app\database;

use PDO;
use PDOException;

class Connection
{

  public static function connect()
  {

    try {
      $pdo = new PDO("mysql:dbname=lista;host=localhost", 'root', '', [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lançar exceções em caso de erro
        PDO::ATTR_EMULATE_PREPARES => false, // Desabilitar emulação de prepared statements
      ]);
      return $pdo;
    } catch (PDOException $e) {
      // Lidar com o erro de conexão (log, mensagem de erro, etc.)
      error_log('Erro de conexão com o banco de dados: ' . $e->getMessage());
      die('Erro de conexão com o banco de dados.');
    }
  }
}
