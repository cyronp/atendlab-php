<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (basename($_SERVER['SCRIPT_NAME']) === 'dashboard.php') {
    header('Location: public/index.php?controller=auth&action=dashboard');
    exit;
}

if (!isset($_SESSION['usuario'])) {
    header('Location: public/index.php?controller=auth&action=login');
    exit;
}

$usuario = $_SESSION['usuario'];
$pathPrefix = (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false) ? '../' : './';
?>

<!DOCTYPE html>
<html lang="en">
  <link rel="stylesheet" href="<?= $pathPrefix ?>assets/css/dashboard.css">

<head>
  <meta charset="UTF-8">
  <title>AtendLab Dashboard</title>
</head>

<body>
  <header class="pessoas-header">
    <h1 class="title">AtendeLab</h1>
    <nav class="header-navigation">
      <a href="?controller=auth&action=dashboard" class="nav-link-active">Home</a>
      <a href="?controller=pessoas&action=visualizar">Gerenciar Pessoas</a>
      <a href="?controller=tipoatendimento&action=visualizar">Gerenciar Tipo</a>
      <a href="?controller=atendimento&action=visualizar">Gerenciar Atendimentos</a>
      <a href="?controller=usuarios&action=visualizar">Gerenciar Usuários</a>
    </nav>
    <div class="header-misc">
      <a href="?controller=auth&action=logout">Sair</a>
    </div>
  </header>
  <main class="dashboard-container">
    <div class="dashboard-title">
      <p>Olá, <?= htmlspecialchars($usuario['nome'] ?? ''); ?>!</p>
    </div>
    <div class="dashboard-cards-container">
      <div class="dashboard-cards">
        <span class="card-title">Atendimentos em Espera</span>
        <p class="card-bignumber" id="espera-count">...</p>
        <a class="card-link" href="?controller=atendimento&action=listar">Acessar Atendimentos</a>
      </div>
      <div class="dashboard-cards">
        <span class="card-title">Atendimentos finalizados este mês</span>
        <p class="card-bignumber" id="finalizados-count">...</p>
        <a class="card-link" href="?controller=atendimento&action=listar">Acessar Atendimentos</a>
      </div>
      <div class="dashboard-cards">
        <span class="card-title">Administradores atendendo</span>
        <p class="card-bignumber" id="admins-count">...</p>
        <a class="card-link" href="?controller=usuarios&action=listar">Acessar Usuários</a>
      </div>
    </div>

    <div class="dashboard-extra-container">
      <h2 class="dashboard-extra-title">Resumo Geral do Sistema</h2>
      <div class="dashboard-extra">
        <div class="extra-card">
          <span class="extra-title">Total de Atendimentos</span>
          <span class="extra-bignumber" id="total-atendimentos">...</span>
        </div>
        <div class="extra-card">
          <span class="extra-title">Pessoas Cadastradas</span>
          <span class="extra-bignumber" id="total-pessoas">...</span>
        </div>
        <div class="extra-card">
          <span class="extra-title">Tipos de Atendimento</span>
          <span class="extra-bignumber" id="total-tipos">...</span>
        </div>
        <div class="extra-card">
          <span class="extra-title">Usuários no Sistema</span>
          <span class="extra-bignumber" id="total-usuarios">...</span>
        </div>

        <div class="extra-card">
          <span class="extra-title">Atendimentos em Andamento</span>
          <span class="extra-bignumber" id="atendimentos-andamento">...</span>
        </div>
        <div class="extra-card">
          <span class="extra-title">Atendimentos Concluídos</span>
          <span class="extra-bignumber" id="atendimentos-concluidos">...</span>
        </div>
        <div class="extra-card">
          <span class="extra-title">Pessoas Ativas</span>
          <span class="extra-bignumber" id="pessoas-ativas">...</span>
        </div>
        <div class="extra-card">
          <span class="extra-title">Usuários Ativos</span>
          <span class="extra-bignumber" id="usuarios-ativos">...</span>
        </div>
      </div>
    </div>
  </main>

  <script src="<?= $pathPrefix ?>assets/js/dashboard.js"></script>
</body>

</html>