document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("tableBody");
    const modal = document.getElementById("pessoaModal");
    const modalTitle = document.getElementById("modalTitle");
    const pessoaForm = document.getElementById("pessoaForm");
    const pessoaIdField = document.getElementById("pessoaId");
    const cpfField = document.getElementById("cpf");
    const telefoneField = document.getElementById("telefone");

    function formatCPF(v) {
        v = v.replace(/\D/g, "");
        if (v.length > 11) v = v.substring(0, 11);
        return v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4") || v;
    }

    function formatPhone(v) {
        v = v.replace(/\D/g, "");
        if (v.length > 11) v = v.substring(0, 11);
        return v.replace(/(\d{2})(\d{4,5})(\d{4})/, "($1) $2-$3") || v;
    }

    function loadPessoas() {
        fetch('?controller=pessoas&action=listar')
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = data.map(p => `
                    <tr>
                        <td class="td-id">#${p.id}</td>
                        <td class="td-name">${p.nome}</td>
                        <td>${formatCPF(p.cpf || '')}</td>
                        <td>${formatPhone(p.telefone || '')}</td>
                        <td class="td-email">${p.email}</td>
                        <td>${p.curso || "-"}</td>
                        <td>${p.periodo || "-"}</td>
                        <td><span class="badge badge-${p.status === 'ativo' ? 'ativo' : 'inativo'}">${p.status === 'ativo' ? 'Ativo' : 'Inativo'}</span></td>
                        <td class="td-actions">
                            <div class="btn-action-group">
                                <button class="btn-action btn-edit" data-id="${p.id}" title="Editar informações">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="9" class="empty-row">Nenhuma pessoa encontrada.</td></tr>';
            });
    }

    cpfField.oninput = (e) => e.target.value = formatCPF(e.target.value);
    telefoneField.oninput = (e) => e.target.value = formatPhone(e.target.value);

    document.getElementById("btnOpenAddModal").onclick = () => {
        pessoaForm.reset();
        modalTitle.textContent = "Cadastrar Nova Pessoa";
        pessoaIdField.value = "";
        document.getElementById("status").value = "ativo";
        modal.classList.add("visible");
    };

    document.getElementById("btnClosedModal").onclick = () => modal.classList.remove("visible");
    document.getElementById("btnCancelModal").onclick = () => modal.classList.remove("visible");

    pessoaForm.onsubmit = (e) => {
        e.preventDefault();
        const action = pessoaIdField.value > 0 ? 'atualizar' : 'criar';
        const formData = new FormData(pessoaForm);
        formData.set("cpf", formData.get("cpf").replace(/\D/g, ""));
        formData.set("telefone", formData.get("telefone").replace(/\D/g, ""));

        fetch(`?controller=pessoas&action=${action}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensagem || data.erro);
            modal.classList.remove("visible");
            loadPessoas();
        });
    };

    tableBody.onclick = (e) => {
        const btnEdit = e.target.closest(".btn-edit");
        if (btnEdit) {
            fetch(`?controller=pessoas&action=buscar&id=${btnEdit.dataset.id}`)
                .then(res => res.json())
                .then(data => {
                    modalTitle.textContent = "Editar Cadastro";
                    pessoaIdField.value = data.id;
                    document.getElementById("nome").value = data.nome || "";
                    document.getElementById("cpf").value = formatCPF(data.cpf || "");
                    document.getElementById("telefone").value = formatPhone(data.telefone || "");
                    document.getElementById("email").value = data.email || "";
                    document.getElementById("curso").value = data.curso || "";
                    document.getElementById("periodo").value = data.periodo || "";
                    document.getElementById("status").value = data.status || "ativo";
                    modal.classList.add("visible");
                });
        }
    };

    loadPessoas();
});
