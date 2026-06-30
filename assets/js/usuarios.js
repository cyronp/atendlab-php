document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("tableBody");
    const modal = document.getElementById("usuarioModal");
    const modalTitle = document.getElementById("modalTitle");
    const usuarioForm = document.getElementById("usuarioForm");
    const usuarioIdField = document.getElementById("usuarioId");
    const senhaGroup = document.getElementById("senhaGroup");
    const senhaField = document.getElementById("senha");

    function loadUsuarios() {
        fetch('?controller=usuarios&action=listar')
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = data.map(u => `
                    <tr>
                        <td class="td-id">#${u.id}</td>
                        <td class="td-name">${u.nome}</td>
                        <td>${u.email}</td>
                        <td style="text-transform: capitalize;">${u.perfil}</td>
                        <td><span class="badge badge-${u.status === 'ativo' ? 'ativo' : 'inativo'}">${u.status === 'ativo' ? 'Ativo' : 'Inativo'}</span></td>
                        <td class="td-actions">
                            <div class="btn-action-group">
                                <button class="btn-action btn-edit" data-id="${u.id}" title="Editar informações">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="6" class="empty-row">Nenhum usuário encontrado.</td></tr>';
            });
    }

    document.getElementById("btnOpenAddModal").onclick = () => {
        usuarioForm.reset();
        modalTitle.textContent = "Cadastrar Novo Usuário";
        usuarioIdField.value = "";
        senhaGroup.style.display = "block";
        senhaField.required = true;
        document.getElementById("status").value = "ativo";
        modal.classList.add("visible");
    };

    document.getElementById("btnClosedModal").onclick = () => modal.classList.remove("visible");
    document.getElementById("btnCancelModal").onclick = () => modal.classList.remove("visible");

    usuarioForm.onsubmit = (e) => {
        e.preventDefault();
        const action = usuarioIdField.value > 0 ? 'atualizar' : 'criar';
        fetch(`?controller=usuarios&action=${action}`, {
            method: 'POST',
            body: new FormData(usuarioForm)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensagem || data.erro);
            modal.classList.remove("visible");
            loadUsuarios();
        });
    };

    tableBody.onclick = (e) => {
        const btnEdit = e.target.closest(".btn-edit");
        if (btnEdit) {
            fetch(`?controller=usuarios&action=buscar&id=${btnEdit.dataset.id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Não foi possível obter dados.');
                    return res.json();
                })
                .then(data => {
                    modalTitle.textContent = "Editar Cadastro";
                    usuarioIdField.value = data.id;
                    document.getElementById("nome").value = data.nome || "";
                    document.getElementById("email").value = data.email || "";
                    senhaGroup.style.display = "none";
                    senhaField.required = false;
                    document.getElementById("perfil").value = data.perfil || "atendente";
                    document.getElementById("status").value = data.status || "ativo";
                    modal.classList.add("visible");
                })
                .catch(err => alert(err.message));
        }
    };

    loadUsuarios();
});
