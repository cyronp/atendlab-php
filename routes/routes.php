<?php

require_once __DIR__ . '/../Controllers/UsuarioController.php';
require_once __DIR__ . '/../Controllers/PessoasController.php';
require_once __DIR__ . '/../Controllers/AtendimentoController.php';
require_once __DIR__ . '/../Controllers/TipoAtendimentoController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$controllerObj = null;

switch ($controller) {

  case 'usuarios':
    $controllerObj = new UsuarioController();
    break;

  case 'pessoas':
    $controllerObj = new PessoasController();
    break;

  case 'tipoatendimento':
    $controllerObj = new TipoAtendimentoController();
    break;

  case 'atendimento':
    $controllerObj = new AtendimentoController();
    break;

  default:
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução.</p>';
    exit;
}

if (!$controllerObj) {
  echo 'Controller não encontrado.';
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

  case 'visualizar':
    if (method_exists($controllerObj, 'visualizar')) {
      $controllerObj->visualizar();
    } else {
      echo 'Ação não suportada.';
    }
    break;

  case 'alterarStatus':
    if (method_exists($controllerObj, 'alterarStatus')) {
      $controllerObj->alterarStatus();
    } else {
      echo 'Ação não suportada.';
    }
    break;

  default:
    echo 'Ação não encontrada.';
}