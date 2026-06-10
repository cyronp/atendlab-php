<?php

require_once __DIR__ . '/../Controllers/usuarioController.php';
require_once __DIR__ . '/../Controllers/pessoasController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($controller) {
  case 'usuarios':
    $controllerObj = new UsuarioController();
    break;

  case 'pessoas':
    $controllerObj = new PessoasController();
    break;

  default:
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução.</p>';
    exit;
}

switch ($action) {
  case 'listar':
    $controllerObj->listar();
    break;

  case 'buscar':
    $controllerObj->buscarPorId();
    break;

  case 'criar':
    $controllerObj->criar();
    break;

  case 'atualizar':
    $controllerObj->atualizar();
    break;

  case 'excluir':
    $controllerObj->excluir();
    break;

  default:
    echo 'Ação não encontrada.';
}
