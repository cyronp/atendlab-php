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
    <title>Gerenciar Usuários - AtendLab</title>
    <link rel="stylesheet" href="<?= $pathPrefix ?>assets/css/pessoas.css">
</head>

<body>

    <header class="pessoas-header">
        <h1 class="title">AtendeLab</h1>
        <nav class="header-navigation">
            <a href="?controller=auth&action=dashboard">Home</a>
            <a href="?controller=pessoas&action=visualizar">Gerenciar Pessoas</a>
            <a href="?controller=tipoatendimento&action=visualizar">Gerenciar Tipo</a>
            <a href="?controller=atendimento&action=visualizar">Gerenciar Atendimentos</a>
            <a href="?controller=usuarios&action=visualizar" class="nav-link-active">Gerenciar Usuários</a>
        </nav>
        <div class="header-misc">
            <a href="?controller=auth&action=logout">Sair</a>
        </div>
    </header>

    <main class="pessoas-container">

        <div class="action-bar">
            <div>
                <h2 class="pessoas-title-main">Gerenciamento de Usuários</h2>
            </div>
            <button class="btn-add" id="btnOpenAddModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Adicionar Usuário
            </button>
        </div>

        <div class="table-card">
            <table class="pessoas-table" id="usuariosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th class="th-actions">Ações</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="6" class="loading-row">Carregando registros...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>

    <div class="modal-backdrop" id="usuarioModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Cadastrar Novo Usuário</h3>
                <button class="modal-close" id="btnClosedModal" aria-label="Fechar Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="usuarioForm">
                <input type="hidden" name="id" id="usuarioId">

                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label for="nome">Nome Completo <span class="required">*</span></label>
                            <input type="text" id="nome" name="nome" class="form-input" required placeholder="Insira o nome completo">
                        </div>

                        <div class="form-group form-group-full">
                            <label for="email">E-mail <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-input" required placeholder="exemplo@atendlab.com">
                        </div>

                        <div class="form-group form-group-full" id="senhaGroup">
                            <label for="senha">Senha <span class="required">*</span></label>
                            <input type="password" id="senha" name="senha" class="form-input" placeholder="Mínimo 6 caracteres">
                        </div>

                        <div class="form-group">
                            <label for="perfil">Perfil <span class="required">*</span></label>
                            <select id="perfil" name="perfil" class="form-select" required>
                                <option value="atendente">Atendente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="form-group">
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
                    <button type="submit" class="btn-save" id="btnSaveUsuario">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let usuariosList = [];
            const tableBody = document.getElementById("tableBody");

            const modal = document.getElementById("usuarioModal");
            const modalTitle = document.getElementById("modalTitle");
            const usuarioForm = document.getElementById("usuarioForm");
            const usuarioIdField = document.getElementById("usuarioId");
            const senhaGroup = document.getElementById("senhaGroup");
            const senhaField = document.getElementById("senha");

            const btnOpenAddModal = document.getElementById("btnOpenAddModal");
            const btnClosedModal = document.getElementById("btnClosedModal");
            const btnCancelModal = document.getElementById("btnCancelModal");

            const loadUsuarios = () => {
                tableBody.innerHTML = '<tr><td colspan="6" class="loading-row">Buscando dados da API...</td></tr>';
                fetch('?controller=usuarios&action=listar')
                    .then(res => {
                        if (!res.ok) throw new Error('Não foi possível listar os usuários.');
                        return res.json();
                    })
                    .then(data => {
                        usuariosList = Array.isArray(data) ? data : [];
                        renderTable(usuariosList);
                    })
                    .catch(err => {
                        console.error(err);
                        alert(err.message || 'Erro ao carregar usuários');
                        tableBody.innerHTML = '<tr><td colspan="6" class="empty-row">Erro ao carregar dados da API. Tente recarregar.</td></tr>';
                    });
            };

            const renderTable = (list) => {
                tableBody.innerHTML = "";
                if (list.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="empty-row">Nenhum usuário encontrado.</td></tr>';
                    return;
                }

                list.forEach(u => {
                    const tr = document.createElement("tr");

                    tr.innerHTML = `
                    <td class="td-id">#${u.id}</td>
                    <td class="td-name">${escapeHtml(u.nome)}</td>
                    <td>${escapeHtml(u.email)}</td>
                    <td style="text-transform: capitalize;">${escapeHtml(u.perfil)}</td>
                    <td>
                        <span class="badge badge-${u.status === 'ativo' ? 'ativo' : 'inativo'}">
                            ${u.status === 'ativo' ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td class="td-actions">
                        <div class="btn-action-group">
                            <button class="btn-action btn-edit" data-id="${u.id}" title="Editar informações">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                            </button>
                        </div>
                    </td>
                `;
                    tableBody.appendChild(tr);
                });
            };

            const escapeHtml = (str) => {
                if (!str) return '';
                return str
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            };

            const openModal = (mode = 'add', data = null) => {
                usuarioForm.reset();

                if (mode === 'add') {
                    modalTitle.textContent = "Cadastrar Novo Usuário";
                    usuarioIdField.value = "";
                    senhaGroup.style.display = "block";
                    senhaField.required = true;
                    document.getElementById("status").value = "ativo";
                } else if (mode === 'edit' && data) {
                    modalTitle.textContent = "Editar Cadastro";
                    usuarioIdField.value = data.id;
                    document.getElementById("nome").value = data.nome || "";
                    document.getElementById("email").value = data.email || "";
                    senhaGroup.style.display = "none";
                    senhaField.required = false;
                    document.getElementById("perfil").value = data.perfil || "atendente";
                    document.getElementById("status").value = data.status || "ativo";
                }

                modal.classList.add("visible");
                document.getElementById("nome").focus();
            };

            const closeModal = () => {
                modal.classList.remove("visible");
            };

            btnOpenAddModal.addEventListener("click", () => openModal('add'));
            btnClosedModal.addEventListener("click", closeModal);
            btnCancelModal.addEventListener("click", closeModal);

            modal.addEventListener("click", (e) => {
                if (e.target === modal) closeModal();
            });

            usuarioForm.addEventListener("submit", (e) => {
                e.preventDefault();

                const isEdit = Math.floor(usuarioIdField.value) > 0;
                const action = isEdit ? 'atualizar' : 'criar';
                const formData = new FormData(usuarioForm);

                fetch(`?controller=usuarios&action=${action}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => {
                        return res.json().then(data => {
                            if (!res.ok) throw new Error(data.erro || 'Erro ao realizar operação.');
                            return data;
                        });
                    })
                    .then(data => {
                        alert(data.mensagem || 'Operação realizada com sucesso!');
                        closeModal();
                        loadUsuarios();
                    })
                    .catch(err => {
                        console.error(err);
                        alert(err.message || 'Falha na comunicação com o servidor.');
                    });
            });

            tableBody.addEventListener("click", (e) => {
                const btnEdit = e.target.closest(".btn-edit");

                if (btnEdit) {
                    const id = btnEdit.dataset.id;
                    fetch(`?controller=usuarios&action=buscar&id=${id}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Não foi possível obter dados do usuário.');
                            return res.json();
                        })
                        .then(data => {
                            openModal('edit', data);
                        })
                        .catch(err => {
                            console.error(err);
                            alert(err.message || 'Erro ao buscar dados.');
                        });
                }
            });

            loadUsuarios();
        });
    </script>
</body>

</html>
