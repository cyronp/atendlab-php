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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let atendimentosList = [];
            const tableBody = document.getElementById("tableBody");

            const modal = document.getElementById("atendimentoModal");
            const modalTitle = document.getElementById("modalTitle");
            const atendimentoForm = document.getElementById("atendimentoForm");
            const atendimentoIdField = document.getElementById("atendimentoId");

            const selectPessoa = document.getElementById("pessoa_id");
            const selectUsuario = document.getElementById("usuario_id");
            const selectTipo = document.getElementById("tipo_atendimento_id");
            const statusField = document.getElementById("status");
            const observacaoField = document.getElementById("observacao");
            const observacaoRequired = document.getElementById("observacaoRequired");

            const btnOpenAddModal = document.getElementById("btnOpenAddModal");
            const btnClosedModal = document.getElementById("btnClosedModal");
            const btnCancelModal = document.getElementById("btnCancelModal");

            // Toggle required indicator for observacao based on status
            statusField.addEventListener("change", () => {
                if (statusField.value === "concluido") {
                    observacaoRequired.style.display = "inline";
                    observacaoField.required = true;
                } else {
                    observacaoRequired.style.display = "none";
                    observacaoField.required = false;
                }
            });

            // Populate select inputs from APIs
            const loadDropdowns = () => {
                // Fetch Pessoas
                fetch('?controller=pessoas&action=listar')
                    .then(res => res.json())
                    .then(data => {
                        selectPessoa.innerHTML = '<option value="">Selecione uma Pessoa...</option>';
                        data.forEach(p => {
                            selectPessoa.innerHTML += `<option value="${p.id}">${escapeHtml(p.nome)} (${escapeHtml(p.cpf)})</option>`;
                        });
                    })
                    .catch(err => console.error('Erro ao carregar pessoas:', err));

                // Fetch Usuarios
                fetch('?controller=usuarios&action=listar')
                    .then(res => res.json())
                    .then(data => {
                        selectUsuario.innerHTML = '<option value="">Selecione um Atendente...</option>';
                        data.forEach(u => {
                            selectUsuario.innerHTML += `<option value="${u.id}">${escapeHtml(u.nome)}</option>`;
                        });
                    })
                    .catch(err => console.error('Erro ao carregar usuários:', err));

                // Fetch Tipos de Atendimento
                fetch('?controller=tipoatendimento&action=listar')
                    .then(res => res.json())
                    .then(data => {
                        selectTipo.innerHTML = '<option value="">Selecione um Tipo...</option>';
                        data.forEach(t => {
                            selectTipo.innerHTML += `<option value="${t.id}">${escapeHtml(t.nome)}</option>`;
                        });
                    })
                    .catch(err => console.error('Erro ao carregar tipos:', err));
            };

            const loadAtendimentos = () => {
                tableBody.innerHTML = '<tr><td colspan="7" class="loading-row">Buscando dados da API...</td></tr>';
                fetch('?controller=atendimento&action=listar')
                    .then(res => {
                        if (!res.ok) throw new Error('Não foi possível listar os atendimentos.');
                        return res.json();
                    })
                    .then(data => {
                        atendimentosList = Array.isArray(data) ? data : [];
                        renderTable(atendimentosList);
                    })
                    .catch(err => {
                        console.error(err);
                        alert(err.message || 'Erro ao carregar atendimentos');
                        tableBody.innerHTML = '<tr><td colspan="7" class="empty-row">Erro ao carregar dados da API. Tente recarregar.</td></tr>';
                    });
            };

            const renderTable = (list) => {
                tableBody.innerHTML = "";
                if (list.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="empty-row">Nenhum atendimento encontrado.</td></tr>';
                    return;
                }

                list.forEach(a => {
                    const tr = document.createElement("tr");

                    // Status Badge color mapping
                    let badgeClass = 'inativo';
                    let statusLabel = 'Aberto';
                    if (a.status === 'aberto') {
                        badgeClass = 'ativo'; 
                        statusLabel = 'Aberto';
                    } else if (a.status === 'em_andamento') {
                        badgeClass = 'andamento';
                        statusLabel = 'Em Andamento';
                    } else if (a.status === 'concluido') {
                        badgeClass = 'ativo';
                        statusLabel = 'Concluído';
                    }

                    const timeDisplay = a.hora_atendimento ? a.hora_atendimento.substring(0, 5) : '';
                    const dateDisplay = a.data_atendimento ? a.data_atendimento.split('-').reverse().join('/') : '';

                    tr.innerHTML = `
                    <td class="td-id">#${a.id}</td>
                    <td class="td-name">${escapeHtml(a.pessoa || 'Sem Pessoa')}</td>
                    <td>${escapeHtml(a.atendente || 'Sem Atendente')}</td>
                    <td>${escapeHtml(a.tipo_atendimento || 'Sem Tipo')}</td>
                    <td>${dateDisplay} ${timeDisplay}</td>
                    <td>
                        <span class="badge badge-${badgeClass}">
                            ${statusLabel}
                        </span>
                    </td>
                    <td class="td-actions">
                        <div class="btn-action-group">
                            <button class="btn-action btn-edit" data-id="${a.id}" title="Editar informações">
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
                atendimentoForm.reset();
                observacaoRequired.style.display = "none";
                observacaoField.required = false;

                if (mode === 'add') {
                    modalTitle.textContent = "Cadastrar Novo Atendimento";
                    atendimentoIdField.value = "";
                    statusField.value = "aberto";
                    
                    // Set current date by default
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById("data_atendimento").value = today;
                    
                    // Set current time by default
                    const now = new Date();
                    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':00';
                    document.getElementById("hora_atendimento").value = timeStr;
                } else if (mode === 'edit' && data) {
                    modalTitle.textContent = "Editar Cadastro";
                    atendimentoIdField.value = data.id;
                    selectPessoa.value = data.pessoa_id || "";
                    selectUsuario.value = data.usuario_id || "";
                    selectTipo.value = data.tipo_atendimento_id || "";
                    document.getElementById("data_atendimento").value = data.data_atendimento || "";
                    document.getElementById("hora_atendimento").value = data.hora_atendimento || "";
                    document.getElementById("descricao").value = data.descricao || "";
                    document.getElementById("observacao").value = data.observacao || "";
                    statusField.value = data.status || "aberto";
                    
                    if (data.status === "concluido") {
                        observacaoRequired.style.display = "inline";
                        observacaoField.required = true;
                    }
                }

                modal.classList.add("visible");
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

            atendimentoForm.addEventListener("submit", (e) => {
                e.preventDefault();

                const isEdit = Math.floor(atendimentoIdField.value) > 0;
                const action = isEdit ? 'atualizar' : 'criar';
                const formData = new FormData(atendimentoForm);

                // Format time value to H:i:s
                let timeVal = formData.get("hora_atendimento");
                if (timeVal) {
                    const parts = timeVal.split(':');
                    if (parts.length === 2) {
                        timeVal += ":00";
                        formData.set("hora_atendimento", timeVal);
                    }
                }

                fetch(`?controller=atendimento&action=${action}`, {
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
                        loadAtendimentos();
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
                    fetch(`?controller=atendimento&action=buscar&id=${id}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Não foi possível obter dados do atendimento.');
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

            loadDropdowns();
            loadAtendimentos();
        });
    </script>
</body>

</html>
