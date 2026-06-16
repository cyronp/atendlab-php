<?php

require_once __DIR__ . '/../Controllers/UsuarioController.php';
require_once __DIR__ . '/../Controllers/PessoasController.php';
require_once __DIR__ . '/../Controllers/AtendimentoController.php';
require_once __DIR__ . '/../Controllers/TipoAtendimentoController.php';
require_once __DIR__ . '/../Controllers/authController.php';
require_once __DIR__ . '/../middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

$controllerObj = null;

switch ($controller) {
  case 'auth':
    $controllerObj = new authController();
    break;

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

  case 'login':
    if (method_exists($controllerObj, 'exibirLogin')) {
      $controllerObj->exibirLogin();
    } else {
      echo 'Ação não suportada.';
    }
    break;

  case 'entrar':
    if (method_exists($controllerObj, 'entrar')) {
      $controllerObj->entrar();
    } else {
      echo 'Ação não suportada.';
    }
    break;

  case 'dashboard':
    if (method_exists($controllerObj, 'dashboard')) {
      $controllerObj->dashboard();
    } else {
      echo 'Ação não suportada.';
    }
    break;

  case 'logout':
    if (method_exists($controllerObj, 'logout')) {
      $controllerObj->logout();
    } else {
      echo 'Ação não suportada.';
    }
    break;

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