<?php

$config = parse_ini_file(__DIR__ . '/../.env');

$host = $config['SERVER_HOST'];
$dbname = $config['DATABASE_NAME'];
$user = $config['DATABASE_USER'];
$password = $config['DATABASE_PASSWORD'];

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $user,
    $password
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die('Erro ao conectar com o banco de dados: ' . $e->getMessage());
}
