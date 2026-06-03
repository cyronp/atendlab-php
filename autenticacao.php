<?php
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

  if (empty($email) || empty($senha)) {
    echo "Por favor preencha e-mail e senha.";
    exit();
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "E-mail inválido.";
    exit();
  }

  $sql = "SELECT id, nome, senha FROM usuarios WHERE email = :email";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['email' => $email]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($usuario && password_verify($senha, $usuario['senha'])) {
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];

    header('Location: dashboard.php');
    exit();
  } else {
    echo "E-mail ou senha inválidos!";
  }
}
