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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let pessoasList = [];
            const tableBody = document.getElementById("tableBody");

            const modal = document.getElementById("pessoaModal");
            const modalTitle = document.getElementById("modalTitle");
            const pessoaForm = document.getElementById("pessoaForm");
            const pessoaIdField = document.getElementById("pessoaId");

            const btnOpenAddModal = document.getElementById("btnOpenAddModal");
            const btnClosedModal = document.getElementById("btnClosedModal");
            const btnCancelModal = document.getElementById("btnCancelModal");

            const cpfField = document.getElementById("cpf");
            const telefoneField = document.getElementById("telefone");

            const loadPessoas = () => {
                tableBody.innerHTML = '<tr><td colspan="9" class="loading-row">Buscando dados da API...</td></tr>';
                fetch('?controller=pessoas&action=listar')
                    .then(res => {
                        if (!res.ok) throw new Error('Não foi possível listar as pessoas.');
                        return res.json();
                    })
                    .then(data => {
                        pessoasList = Array.isArray(data) ? data : [];
                        renderTable(pessoasList);
                    })
                    .catch(err => {
                        console.error(err);
                        alert(err.message || 'Erro ao carregar pessoas');
                        tableBody.innerHTML = '<tr><td colspan="9" class="empty-row">Erro ao carregar dados da API. Tente recarregar.</td></tr>';
                    });
            };

            const formatCPF = (v) => {
                v = v.replace(/\D/g, "");
                if (v.length > 11) v = v.substring(0, 11);
                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
                return v;
            };

            const formatPhone = (v) => {
                v = v.replace(/\D/g, "");
                if (v.length > 11) v = v.substring(0, 11);
                v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
                if (v.length > 13) {
                    v = v.replace(/(\d{5})(\d{4})$/, "$1-$2");
                } else {
                    v = v.replace(/(\d{4})(\d{4})$/, "$1-$2");
                }
                return v;
            };

            cpfField.addEventListener("input", (e) => {
                e.target.value = formatCPF(e.target.value);
            });

            telefoneField.addEventListener("input", (e) => {
                e.target.value = formatPhone(e.target.value);
            });

            const renderTable = (list) => {
                tableBody.innerHTML = "";
                if (list.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="9" class="empty-row">Nenhuma pessoa encontrada.</td></tr>';
                    return;
                }

                list.forEach(p => {
                    const tr = document.createElement("tr");

                    const cpfFormatted = formatCPF(p.cpf || "");
                    const phoneFormatted = formatPhone(p.telefone || "");

                    tr.innerHTML = `
                    <td class="td-id">#${p.id}</td>
                    <td class="td-name">${escapeHtml(p.nome)}</td>
                    <td>${escapeHtml(cpfFormatted)}</td>
                    <td>${escapeHtml(phoneFormatted)}</td>
                    <td class="td-email">${escapeHtml(p.email)}</td>
                    <td>${escapeHtml(p.curso || "-")}</td>
                    <td>${escapeHtml(p.periodo || "-")}</td>
                    <td>
                        <span class="badge badge-${p.status === 'ativo' ? 'ativo' : 'inativo'}">
                            ${p.status === 'ativo' ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td class="td-actions">
                        <div class="btn-action-group">
                            <button class="btn-action btn-edit" data-id="${p.id}" title="Editar informações">
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
                pessoaForm.reset();

                if (mode === 'add') {
                    modalTitle.textContent = "Cadastrar Nova Pessoa";
                    pessoaIdField.value = "";
                    document.getElementById("status").value = "ativo";
                } else if (mode === 'edit' && data) {
                    modalTitle.textContent = "Editar Cadastro";
                    pessoaIdField.value = data.id;
                    document.getElementById("nome").value = data.nome || "";
                    document.getElementById("cpf").value = formatCPF(data.cpf || "");
                    document.getElementById("telefone").value = formatPhone(data.telefone || "");
                    document.getElementById("email").value = data.email || "";
                    document.getElementById("curso").value = data.curso || "";
                    document.getElementById("periodo").value = data.periodo || "";
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

            pessoaForm.addEventListener("submit", (e) => {
                e.preventDefault();

                const isEdit = Math.floor(pessoaIdField.value) > 0;
                const action = isEdit ? 'atualizar' : 'criar';
                const formData = new FormData(pessoaForm);

                const cleanCpf = formData.get("cpf").replace(/\D/g, "");
                const cleanPhone = formData.get("telefone").replace(/\D/g, "");
                formData.set("cpf", cleanCpf);
                formData.set("telefone", cleanPhone);

                fetch(`?controller=pessoas&action=${action}`, {
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
                        loadPessoas();
                    })
                    .catch(err => {
                        console.error(err);
                        alert(err.message || 'Falha na comunicação com o servidor.');
                    });
            });

            tableBody.addEventListener("click", (e) => {
                const btnEdit = e.target.closest(".btn-edit");
                const btnDelete = e.target.closest(".btn-delete");

                if (btnEdit) {
                    const id = btnEdit.dataset.id;
                    fetch(`?controller=pessoas&action=buscar&id=${id}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Não foi possível obter dados da pessoa.');
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

                if (btnDelete) {
                    const id = btnDelete.dataset.id;
                    const person = pessoasList.find(p => p.id == id);
                    const name = person ? person.nome : 'esta pessoa';

                    if (confirm(`Deseja realmente inativar o cadastro de "${name}"?`)) {
                        const params = new FormData();
                        params.append('id', id);

                        fetch('?controller=pessoas&action=excluir', {
                                method: 'POST',
                                body: params
                            })
                            .then(res => {
                                return res.json().then(data => {
                                    if (!res.ok) throw new Error(data.erro || 'Erro ao inativar registro.');
                                    return data;
                                });
                            })
                            .then(data => {
                                alert(data.mensagem || 'Registro inativado com sucesso!');
                                loadPessoas();
                            })
                            .catch(err => {
                                console.error(err);
                                alert(err.message || 'Falha ao inativar registro.');
                            });
                    }
                }
            });

            loadPessoas();
        });
    </script>
</body>

</html>