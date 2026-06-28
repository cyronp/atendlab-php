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
        <!-- Linha 1: Totais do Sistema -->
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

        <!-- Linha 2: Métricas Detalhadas -->
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

  <script>
    document.addEventListener("DOMContentLoaded", () => {
        Promise.all([
            fetch('?controller=atendimento&action=listar').then(res => {
                if (!res.ok) throw new Error('Erro ao carregar atendimentos');
                return res.json();
            }),
            fetch('?controller=pessoas&action=listar').then(res => {
                if (!res.ok) throw new Error('Erro ao carregar pessoas');
                return res.json();
            }),
            fetch('?controller=tipoatendimento&action=listar').then(res => {
                if (!res.ok) throw new Error('Erro ao carregar tipos de atendimento');
                return res.json();
            }),
            fetch('?controller=usuarios&action=listar').then(res => {
                if (!res.ok) throw new Error('Erro ao carregar usuários');
                return res.json();
            })
        ])
        .then(([atendimentos, pessoas, tipos, usuarios]) => {
            const esperaCount = atendimentos.filter(a => a.status === 'aberto').length;
            document.getElementById('espera-count').textContent = esperaCount;

            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth() + 1;
            
            const finalizadosCount = atendimentos.filter(a => {
                if (a.status !== 'concluido') return false;
                if (!a.data_atendimento) return false;
                const parts = a.data_atendimento.split('-');
                if (parts.length < 2) return false;
                const yr = parseInt(parts[0], 10);
                const mo = parseInt(parts[1], 10);
                return yr === currentYear && mo === currentMonth;
            }).length;
            document.getElementById('finalizados-count').textContent = finalizadosCount;

            const adminsCount = usuarios.filter(u => u.perfil === 'admin' && u.status === 'ativo').length;
            document.getElementById('admins-count').textContent = adminsCount;

            document.getElementById('total-atendimentos').textContent = atendimentos.length;
            document.getElementById('total-pessoas').textContent = pessoas.length;
            document.getElementById('total-tipos').textContent = tipos.length;
            document.getElementById('total-usuarios').textContent = usuarios.length;

            const andamentoCount = atendimentos.filter(a => a.status === 'em_andamento').length;
            document.getElementById('atendimentos-andamento').textContent = andamentoCount;

            const concluidosCount = atendimentos.filter(a => a.status === 'concluido').length;
            document.getElementById('atendimentos-concluidos').textContent = concluidosCount;

            const pessoasAtivas = pessoas.filter(p => p.status === 'ativo').length;
            document.getElementById('pessoas-ativas').textContent = pessoasAtivas;

            const usuariosAtivos = usuarios.filter(u => u.status === 'ativo').length;
            document.getElementById('usuarios-ativos').textContent = usuariosAtivos;
        })
        .catch(err => {
            console.error('Falha ao inicializar o dashboard:', err);
            const errFields = [
                'espera-count', 'finalizados-count', 'admins-count',
                'total-atendimentos', 'total-pessoas', 'total-tipos', 'total-usuarios',
                'atendimentos-andamento', 'atendimentos-concluidos', 'pessoas-ativas', 'usuarios-ativos'
            ];
            errFields.forEach(f => {
                const el = document.getElementById(f);
                if (el) el.textContent = 'Erro';
            });
        });
    });
  </script>
</body>

</html>