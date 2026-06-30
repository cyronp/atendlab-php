<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: public/index.php?controller=auth&action=login');
    exit;
}

$usuario = $_SESSION['usuario'];
$pathPrefix = (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false) ? '../' : './';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Atendimentos - AtendLab</title>
    <link rel="stylesheet" href="<?= $pathPrefix ?>assets/css/pessoas.css">
</head>

<body>

    <header class="pessoas-header">
        <h1 class="title">AtendeLab</h1>
        <nav class="header-navigation">
            <a href="?controller=auth&action=dashboard">Home</a>
            <a href="?controller=pessoas&action=visualizar">Gerenciar Pessoas</a>
            <a href="?controller=tipoatendimento&action=visualizar">Gerenciar Tipo</a>
            <a href="?controller=atendimento&action=visualizar" class="nav-link-active">Gerenciar Atendimentos</a>
            <a href="?controller=usuarios&action=visualizar">Gerenciar Usuários</a>
        </nav>
        <div class="header-misc">
            <a href="?controller=auth&action=logout">Sair</a>
        </div>
    </header>

    <main class="pessoas-container">

        <div class="action-bar">
            <div>
                <h2 class="pessoas-title-main">Gerenciamento de Atendimentos</h2>
            </div>
            <button class="btn-add" id="btnOpenAddModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Novo Atendimento
            </button>
        </div>

        <div class="table-card">
            <table class="pessoas-table" id="atendimentosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pessoa</th>
                        <th>Atendente</th>
                        <th>Tipo</th>
                        <th>Data/Hora</th>
                        <th>Status</th>
                        <th class="th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="7" class="loading-row">Carregando registros...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>

    <div class="modal-backdrop" id="atendimentoModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Cadastrar Novo Atendimento</h3>
                <button class="modal-close" id="btnClosedModal" aria-label="Fechar Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="atendimentoForm">
                <input type="hidden" name="id" id="atendimentoId">

                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="pessoa_id">Pessoa <span class="required">*</span></label>
                            <select id="pessoa_id" name="pessoa_id" class="form-select" required>
                                <option value="">Carregando pessoa...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="usuario_id">Atendente <span class="required">*</span></label>
                            <select id="usuario_id" name="usuario_id" class="form-select" required>
                                <option value="">Carregando atendentes...</option>
                            </select>
                        </div>

                        <div class="form-group form-group-full">
                            <label for="tipo_atendimento_id">Tipo de Atendimento <span class="required">*</span></label>
                            <select id="tipo_atendimento_id" name="tipo_atendimento_id" class="form-select" required>
                                <option value="">Carregando tipos...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="data_atendimento">Data <span class="required">*</span></label>
                            <input type="date" id="data_atendimento" name="data_atendimento" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="hora_atendimento">Hora</label>
                            <input type="time" id="hora_atendimento" name="hora_atendimento" class="form-input" step="1">
                        </div>

                        <div class="form-group form-group-full">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" class="form-input" placeholder="Detalhes do atendimento" style="height: 80px; resize: vertical; font-family: inherit;"></textarea>
                        </div>

                        <div class="form-group form-group-full">
                            <label for="observacao">Observação Final <span class="required" id="observacaoRequired" style="display: none;">*</span></label>
                            <textarea id="observacao" name="observacao" class="form-input" placeholder="Obrigatório para atendimentos concluídos" style="height: 80px; resize: vertical; font-family: inherit;"></textarea>
                        </div>

                        <div class="form-group form-group-full">
                            <label for="status">Status <span class="required">*</span></label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="aberto">Aberto</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluido">Concluído</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelModal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnSaveAtendimento">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= $pathPrefix ?>assets/js/atendimentos.js"></script>
</body>

</html>
