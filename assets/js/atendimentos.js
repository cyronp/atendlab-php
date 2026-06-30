document.addEventListener("DOMContentLoaded", () => {
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

    statusField.onchange = () => {
        const isConcluido = statusField.value === "concluido";
        observacaoRequired.style.display = isConcluido ? "inline" : "none";
        observacaoField.required = isConcluido;
    };

    function loadDropdowns() {
        fetch('?controller=pessoas&action=listar')
            .then(res => res.json())
            .then(data => {
                selectPessoa.innerHTML = '<option value="">Selecione uma Pessoa...</option>' +
                    data.map(p => `<option value="${p.id}">${p.nome} (${p.cpf})</option>`).join('');
            });

        fetch('?controller=usuarios&action=listar')
            .then(res => res.json())
            .then(data => {
                selectUsuario.innerHTML = '<option value="">Selecione um Atendente...</option>' +
                    data.map(u => `<option value="${u.id}">${u.nome}</option>`).join('');
            });

        fetch('?controller=tipoatendimento&action=listar')
            .then(res => res.json())
            .then(data => {
                selectTipo.innerHTML = '<option value="">Selecione um Tipo...</option>' +
                    data.map(t => `<option value="${t.id}">${t.nome}</option>`).join('');
            });
    }

    function loadAtendimentos() {
        fetch('?controller=atendimento&action=listar')
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = data.map(a => {
                    const badgeClass = a.status === 'aberto' ? 'ativo' : (a.status === 'em_andamento' ? 'andamento' : 'ativo');
                    const statusLabel = a.status === 'aberto' ? 'Aberto' : (a.status === 'em_andamento' ? 'Em Andamento' : 'Concluído');
                    const timeDisplay = a.hora_atendimento ? a.hora_atendimento.substring(0, 5) : '';
                    const dateDisplay = a.data_atendimento ? a.data_atendimento.split('-').reverse().join('/') : '';

                    return `
                        <tr>
                            <td class="td-id">#${a.id}</td>
                            <td class="td-name">${a.pessoa || 'Sem Pessoa'}</td>
                            <td>${a.atendente || 'Sem Atendente'}</td>
                            <td>${a.tipo_atendimento || 'Sem Tipo'}</td>
                            <td>${dateDisplay} ${timeDisplay}</td>
                            <td><span class="badge badge-${badgeClass}">${statusLabel}</span></td>
                            <td class="td-actions">
                                <div class="btn-action-group">
                                    <button class="btn-action btn-edit" data-id="${a.id}" title="Editar informações">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('') || '<tr><td colspan="7" class="empty-row">Nenhum atendimento encontrado.</td></tr>';
            });
    }

    document.getElementById("btnOpenAddModal").onclick = () => {
        atendimentoForm.reset();
        modalTitle.textContent = "Cadastrar Novo Atendimento";
        atendimentoIdField.value = "";
        statusField.value = "aberto";
        observacaoRequired.style.display = "none";
        observacaoField.required = false;
        
        document.getElementById("data_atendimento").value = new Date().toISOString().split('T')[0];
        const now = new Date();
        document.getElementById("hora_atendimento").value = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':00';
        modal.classList.add("visible");
    };

    document.getElementById("btnClosedModal").onclick = () => modal.classList.remove("visible");
    document.getElementById("btnCancelModal").onclick = () => modal.classList.remove("visible");

    atendimentoForm.onsubmit = (e) => {
        e.preventDefault();
        const action = atendimentoIdField.value > 0 ? 'atualizar' : 'criar';
        const formData = new FormData(atendimentoForm);
        let timeVal = formData.get("hora_atendimento");
        if (timeVal && timeVal.split(':').length === 2) {
            formData.set("hora_atendimento", timeVal + ":00");
        }

        fetch(`?controller=atendimento&action=${action}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensagem || data.erro);
            modal.classList.remove("visible");
            loadAtendimentos();
        });
    };

    tableBody.onclick = (e) => {
        const btnEdit = e.target.closest(".btn-edit");
        if (btnEdit) {
            fetch(`?controller=atendimento&action=buscar&id=${btnEdit.dataset.id}`)
                .then(res => res.json())
                .then(data => {
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
                    
                    const isConcluido = data.status === "concluido";
                    observacaoRequired.style.display = isConcluido ? "inline" : "none";
                    observacaoField.required = isConcluido;
                    modal.classList.add("visible");
                });
        }
    };

    loadDropdowns();
    loadAtendimentos();
});
