document.addEventListener("DOMContentLoaded", () => {
    Promise.all([
        fetch('?controller=atendimento&action=listar').then(res => res.json()),
        fetch('?controller=pessoas&action=listar').then(res => res.json()),
        fetch('?controller=tipoatendimento&action=listar').then(res => res.json()),
        fetch('?controller=usuarios&action=listar').then(res => res.json())
    ])
    .then(([atendimentos, pessoas, tipos, usuarios]) => {
        document.getElementById('espera-count').textContent = atendimentos.filter(a => a.status === 'aberto').length;
        
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth() + 1;
        document.getElementById('finalizados-count').textContent = atendimentos.filter(a => {
            if (a.status !== 'concluido' || !a.data_atendimento) return false;
            const parts = a.data_atendimento.split('-');
            return parseInt(parts[0], 10) === currentYear && parseInt(parts[1], 10) === currentMonth;
        }).length;

        document.getElementById('admins-count').textContent = usuarios.filter(u => u.perfil === 'admin' && u.status === 'ativo').length;
        document.getElementById('total-atendimentos').textContent = atendimentos.length;
        document.getElementById('total-pessoas').textContent = pessoas.length;
        document.getElementById('total-tipos').textContent = tipos.length;
        document.getElementById('total-usuarios').textContent = usuarios.length;
        document.getElementById('atendimentos-andamento').textContent = atendimentos.filter(a => a.status === 'em_andamento').length;
        document.getElementById('atendimentos-concluidos').textContent = atendimentos.filter(a => a.status === 'concluido').length;
        document.getElementById('pessoas-ativas').textContent = pessoas.filter(p => p.status === 'ativo').length;
        document.getElementById('usuarios-ativos').textContent = usuarios.filter(u => u.status === 'ativo').length;
    });
});
