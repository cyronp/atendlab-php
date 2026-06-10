<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <link rel="stylesheet" href="./assets/css/dashboard.css">

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
      <a>Sair</a>
      <a>Ajuda</a>
    </div>
  </header>
  <main>
    <div class="dashboard-title">
      <p>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']); ?></p>
    </div>
    <div class="dashboard-container">
      <p>Teste</p>
    </div>
  </main>
</body>

</html>