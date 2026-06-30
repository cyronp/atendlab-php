document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("tableBody");
    const modal = document.getElementById("tipoModal");
    const modalTitle = document.getElementById("modalTitle");
    const tipoForm = document.getElementById("tipoForm");
    const tipoIdField = document.getElementById("tipoId");

    function loadTipos() {
        fetch('?controller=tipoatendimento&action=listar')
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = data.map(t => `
                    <tr>
                        <td class="td-id">#${t.id}</td>
                        <td class="td-name">${t.nome}</td>
                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${t.descricao || ''}</td>
                        <td><span class="badge badge-${t.status === 'ativo' ? 'ativo' : 'inactive'}">${t.status === 'ativo' ? 'Ativo' : 'Inativo'}</span></td>
                        <td class="td-actions">
                            <div class="btn-action-group">
                                <button class="btn-action btn-edit" data-id="${t.id}" title="Editar informações">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="5" class="empty-row">Nenhum tipo de atendimento encontrado.</td></tr>';
            });
    }

    document.getElementById("btnOpenAddModal").onclick = () => {
        tipoForm.reset();
        modalTitle.textContent = "Cadastrar Novo Tipo";
        tipoIdField.value = "";
        document.getElementById("status").value = "ativo";
        modal.classList.add("visible");
    };

    document.getElementById("btnClosedModal").onclick = () => modal.classList.remove("visible");
    document.getElementById("btnCancelModal").onclick = () => modal.classList.remove("visible");

    tipoForm.onsubmit = (e) => {
        e.preventDefault();
        const action = tipoIdField.value > 0 ? 'atualizar' : 'criar';
        fetch(`?controller=tipoatendimento&action=${action}`, {
            method: 'POST',
            body: new FormData(tipoForm)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensagem || data.erro);
            modal.classList.remove("visible");
            loadTipos();
        });
    };

    tableBody.onclick = (e) => {
        const btnEdit = e.target.closest(".btn-edit");
        if (btnEdit) {
            fetch(`?controller=tipoatendimento&action=buscar&id=${btnEdit.dataset.id}`)
                .then(res => res.json())
                .then(data => {
                    modalTitle.textContent = "Editar Cadastro";
                    tipoIdField.value = data.id;
                    document.getElementById("nome").value = data.nome || "";
                    document.getElementById("descricao").value = data.descricao || "";
                    document.getElementById("status").value = data.status || "ativo";
                    modal.classList.add("visible");
                });
        }
    };

    loadTipos();
});
