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
    <title>Gerenciar Pessoas - AtendLab</title>
    <link rel="stylesheet" href="<?= $pathPrefix ?>assets/css/pessoas.css">
</head>

<body>

    <header class="pessoas-header">
        <h1 class="title">AtendeLab</h1>
        <nav class="header-navigation">
            <a href="?controller=auth&action=dashboard">Home</a>
            <a href="?controller=pessoas&action=visualizar" class="nav-link-active">Gerenciar Pessoas</a>
            <a href="?controller=tipoatendimento&action=visualizar">Gerenciar Tipo</a>
            <a href="?controller=atendimento&action=visualizar">Gerenciar Atendimentos</a>
            <a href="?controller=usuarios&action=visualizar">Gerenciar Usuários</a>
        </nav>
        <div class="header-misc">
            <a href="?controller=auth&action=logout">Sair</a>
        </div>
    </header>

    <main class="pessoas-container">

        <div class="action-bar">
            <div>
                <h2 class="pessoas-title-main">Gerenciamento de Pessoas</h2>
            </div>
            <button class="btn-add" id="btnOpenAddModal">
                Adicionar Pessoa
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
            </button>
        </div>

        <div class="table-card">
            <table class="pessoas-table" id="pessoasTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Curso</th>
                        <th>Período</th>
                        <th>Status</th>
                        <th class="th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="9" class="loading-row">Carregando registros...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>

    <div class="modal-backdrop" id="pessoaModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Cadastrar Nova Pessoa</h3>
                <button class="modal-close" id="btnClosedModal" aria-label="Fechar Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="pessoaForm">
                <input type="hidden" name="id" id="pessoaId">

                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label for="nome">Nome Completo <span class="required">*</span></label>
                            <input type="text" id="nome" name="nome" class="form-input" required placeholder="Insira o nome completo">
                        </div>

                        <div class="form-group">
                            <label for="cpf">CPF <span class="required">*</span></label>
                            <input type="text" id="cpf" name="cpf" class="form-input" required placeholder="000.000.000-00" maxlength="14">
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone <span class="required">*</span></label>
                            <input type="text" id="telefone" name="telefone" class="form-input" required placeholder="(00) 00000-0000" maxlength="15">
                        </div>

                        <div class="form-group form-group-full">
                            <label for="email">E-mail <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-input" required placeholder="exemplo@atendlab.com">
                        </div>

                        <div class="form-group">
                            <label for="curso">Curso</label>
                            <input type="text" id="curso" name="curso" class="form-input" placeholder="Ex: Engenharia de Software">
                        </div>

                        <div class="form-group">
                            <label for="periodo">Período</label>
                            <input type="text" id="periodo" name="periodo" class="form-input" placeholder="Ex: 3">
                        </div>

                        <div class="form-group form-group-full">
                            <label for="status">Status <span class="required">*</span></label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelModal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnSavePessoa">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= $pathPrefix ?>assets/js/pessoas.js"></script>
</body>

</html>