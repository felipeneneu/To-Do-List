<?php
// Config
$db = 'lista';
$server = 'localhost';
$user = 'root';
$password = '';


// Definindo o fuso horário para São Paulo
date_default_timezone_set('America/Sao_Paulo');

try {
  $pdo = new PDO("mysql:dbname=$db;host=$server", "$user", "$password");
} catch (Exception $e) {
  echo "Erro ao conectar ao banco de dados!<br>" . $e;
}
