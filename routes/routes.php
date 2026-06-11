<?php

require_once __DIR__ . '/../Controllers/usuarioController.php';
require_once __DIR__ . '/../Controllers/pessoasController.php';
require_once __DIR__ . '/../Controllers/atendimentoController.php';
require_once __DIR__ . '/../Controllers/tipoAtendimentoController.php';


$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($controller) {
  case 'usuarios':
    $controllerObj = new UsuarioController();
    break;

  case 'pessoas':
    $controllerObj = new PessoasController();
    break;
  
  case 'tpatendimento':
    $controllerObj = new tipoAtendimentoController();
    break;
  case 'atendimento':
    $controllerObj = new atendimentoController();
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

  case 'visualizar':
    $controllerObj->visualizar();
    break;

  case 'alterarStatus':
    $controllerObj->alterarStatus();
    break;

  default:
    echo 'Ação não encontrada.';
}
