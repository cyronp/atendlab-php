<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If accessed directly instead of through the router
if (basename($_SERVER['SCRIPT_NAME']) === 'dashboard.php') {
    header('Location: public/index.php?controller=auth&action=dashboard');
    exit;
}

// Fallback check (though middleware handles this)
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
  <header class="dashboard-header">
    <h1 class="title">AtendeLab</h1>
    <nav class="header-navigation">
      <a>Home</a>
      <a>Pessoas</a>
      <a>Tipo atendimentos</a>
      <a>Atendimentos</a>
      <a>Relatórios</a>
    </nav>
    <div class="header-misc">
      <a href="?controller=auth&action=logout">Sair</a>
      <a>Ajuda</a>
    </div>
  </header>
  <main>
    <div class="dashboard-title">
      <p>Olá, <?= htmlspecialchars($usuario['nome'] ?? ''); ?></p>
    </div>
    <div class="dashboard-container">
      <p>Teste</p>
    </div>
  </main>
</body>

</html>