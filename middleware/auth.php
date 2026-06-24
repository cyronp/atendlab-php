<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function usuarioAutenticado(): bool
{
  return isset($_SESSION['usuario'])
    && is_array($_SESSION['usuario']);
}

function exigirAutenticacao(): void
{
  if (!usuarioAutenticado()){
    $controller = $_GET['controller'] ?? 'auth';
    if ($controller !== 'auth') {
      http_response_code(401);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['erro' => 'Não autorizado. Faça login para acessar.'], JSON_UNESCAPED_UNICODE);
      exit;
    }

    $_SESSION['mensagem'] = 'Faca login para acessar a area restrita.';

    header('Location: ?controller=auth&action=login');
    exit;
  }
}

function usuarioAtual(): ?array
{
  return $_SESSION['usuario'] ?? null;
}