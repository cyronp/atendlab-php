<?php

class atendimentoController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $sql = "
                SELECT
                    a.id,
                    a.pessoa_id,
                    p.nome AS pessoa,
                    a.usuario_id,
                    u.nome AS atendente,
                    a.tipo_atendimento_id,
                    t.nome AS tipo_atendimento,
                    a.data_atendimento,
                    a.hora_atendimento,
                    a.descricao,
                    a.observacao,
                    a.status,
                    a.criado_em
                FROM atendimentos a
                INNER JOIN pessoas p
                    ON p.id = a.pessoa_id
                INNER JOIN usuarios u
                    ON u.id = a.usuario_id
                INNER JOIN tipos_atendimentos t
                    ON t.id = a.tipo_atendimento_id
                ORDER BY a.id DESC
            ";

            $stmt = $this->pdo->query($sql);

            echo json_encode(
                $stmt->fetchAll(PDO::FETCH_ASSOC),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );
        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao listar atendimentos.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);

            echo json_encode([
                'erro' => 'ID inválido.'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        try {

            $sql = "
                SELECT
                    a.id,
                    a.pessoa_id,
                    p.nome AS pessoa,
                    a.usuario_id,
                    u.nome AS atendente,
                    a.tipo_atendimento_id,
                    t.nome AS tipo_atendimento,
                    a.data_atendimento,
                    a.hora_atendimento,
                    a.descricao,
                    a.observacao,
                    a.status,
                    a.criado_em
                FROM atendimentos a
                INNER JOIN pessoas p
                    ON p.id = a.pessoa_id
                INNER JOIN usuarios u
                    ON u.id = a.usuario_id
                INNER JOIN tipos_atendimentos t
                    ON t.id = a.tipo_atendimento_id
                WHERE a.id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$atendimento) {

                http_response_code(404);

                echo json_encode([
                    'erro' => 'Atendimento não encontrado.'
                ], JSON_UNESCAPED_UNICODE);

                return;
            }

            echo json_encode(
                $atendimento,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );
        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao buscar atendimento.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);

        $data_atendimento = $_POST['data_atendimento'] ?? '';
        $hora_atendimento = $_POST['hora_atendimento'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        $status = strtolower(trim($_POST['status'] ?? 'aberto'));

        if (!$pessoa_id || !$usuario_id || !$tipo_atendimento_id) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Pessoa, usuário e tipo de atendimento são obrigatórios.'
            ]);

            return;
        }

        if (
            !DateTime::createFromFormat('Y-m-d', $data_atendimento)
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Data inválida.'
            ]);

            return;
        }

        if (
            !empty($hora_atendimento) &&
            !DateTime::createFromFormat('H:i:s', $hora_atendimento)
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Hora inválida. Utilize H:i:s.'
            ]);

            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Status inválido.'
            ]);

            return;
        }

        if ($status === 'concluido' && $observacao === '') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Observação final é obrigatória para atendimentos concluídos.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $stmtPessoa = $this->pdo->prepare('SELECT status FROM pessoas WHERE id = :id');
        $stmtPessoa->execute([':id' => $pessoa_id]);
        if (!$stmtPessoa->fetch()) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Pessoa atendida não encontrada.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $stmtTipo = $this->pdo->prepare('SELECT status FROM tipos_atendimentos WHERE id = :id');
        $stmtTipo->execute([':id' => $tipo_atendimento_id]);
        if (!$stmtTipo->fetch()) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Tipo de atendimento não encontrado.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $stmtUsuario = $this->pdo->prepare('SELECT status FROM usuarios WHERE id = :id');
        $stmtUsuario->execute([':id' => $usuario_id]);
        $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Usuário responsável não encontrado.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($usuario['status'] !== 'ativo') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Usuário responsável está inativo.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {

            $sql = "
                INSERT INTO atendimentos (
                    pessoa_id,
                    usuario_id,
                    tipo_atendimento_id,
                    data_atendimento,
                    hora_atendimento,
                    descricao,
                    observacao,
                    status
                )
                VALUES (
                    :pessoa_id,
                    :usuario_id,
                    :tipo_atendimento_id,
                    :data_atendimento,
                    :hora_atendimento,
                    :descricao,
                    :observacao,
                    :status
                )
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':pessoa_id' => $pessoa_id,
                ':usuario_id' => $usuario_id,
                ':tipo_atendimento_id' => $tipo_atendimento_id,
                ':data_atendimento' => $data_atendimento,
                ':hora_atendimento' => $hora_atendimento ?: null,
                ':descricao' => $descricao,
                ':observacao' => $observacao,
                ':status' => $status
            ]);

            http_response_code(201);

            echo json_encode([
                'mensagem' => 'Atendimento cadastrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao cadastrar atendimento.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);

        $data_atendimento = $_POST['data_atendimento'] ?? '';
        $hora_atendimento = $_POST['hora_atendimento'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        $status = strtolower(trim($_POST['status'] ?? 'aberto'));

        if (!$id || !$pessoa_id || !$usuario_id || !$tipo_atendimento_id) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Dados obrigatórios não informados.'
            ]);

            return;
        }

        if (
            !DateTime::createFromFormat('Y-m-d', $data_atendimento)
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Data inválida.'
            ]);

            return;
        }

        if (
            !empty($hora_atendimento) &&
            !DateTime::createFromFormat('H:i:s', $hora_atendimento)
        ) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Hora inválida.'
            ]);

            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Status inválido.'
            ]);

            return;
        }

        if ($status === 'concluido' && $observacao === '') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Observação final é obrigatória para atendimentos concluídos.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $stmtPessoa = $this->pdo->prepare('SELECT status FROM pessoas WHERE id = :id');
        $stmtPessoa->execute([':id' => $pessoa_id]);
        if (!$stmtPessoa->fetch()) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Pessoa atendida não encontrada.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $stmtTipo = $this->pdo->prepare('SELECT status FROM tipos_atendimentos WHERE id = :id');
        $stmtTipo->execute([':id' => $tipo_atendimento_id]);
        if (!$stmtTipo->fetch()) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Tipo de atendimento não encontrado.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $stmtUsuario = $this->pdo->prepare('SELECT status FROM usuarios WHERE id = :id');
        $stmtUsuario->execute([':id' => $usuario_id]);
        $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Usuário responsável não encontrado.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($usuario['status'] !== 'ativo') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Usuário responsável está inativo.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {

            $stmt = $this->pdo->prepare(
                'SELECT id FROM atendimentos WHERE id = :id'
            );

            $stmt->execute([
                ':id' => $id
            ]);

            if (!$stmt->fetch()) {

                http_response_code(404);

                echo json_encode([
                    'erro' => 'Atendimento não encontrado.'
                ]);

                return;
            }

            $sql = "
                UPDATE atendimentos
                SET
                    pessoa_id = :pessoa_id,
                    usuario_id = :usuario_id,
                    tipo_atendimento_id = :tipo_atendimento_id,
                    data_atendimento = :data_atendimento,
                    hora_atendimento = :hora_atendimento,
                    descricao = :descricao,
                    observacao = :observacao,
                    status = :status
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id,
                ':pessoa_id' => $pessoa_id,
                ':usuario_id' => $usuario_id,
                ':tipo_atendimento_id' => $tipo_atendimento_id,
                ':data_atendimento' => $data_atendimento,
                ':hora_atendimento' => $hora_atendimento ?: null,
                ':descricao' => $descricao,
                ':observacao' => $observacao,
                ':status' => $status
            ]);

            echo json_encode([
                'mensagem' => 'Atendimento atualizado com sucesso.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao atualizar atendimento.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function alterarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = strtolower(trim($_POST['status'] ?? 'aberto'));

        if (!$id) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'ID inválido.'
            ]);

            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {

            http_response_code(400);

            echo json_encode([
                'erro' => 'Status inválido.'
            ]);

            return;
        }

        try {

            $stmt = $this->pdo->prepare(
                'SELECT id, observacao FROM atendimentos WHERE id = :id'
            );

            $stmt->execute([
                ':id' => $id
            ]);

            $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$atendimento) {

                http_response_code(404);

                echo json_encode([
                    'erro' => 'Atendimento não encontrado.'
                ]);

                return;
            }

            $observacao = trim($_POST['observacao'] ?? $atendimento['observacao'] ?? '');

            if ($status === 'concluido' && $observacao === '') {
                http_response_code(400);
                echo json_encode([
                    'erro' => 'Observação final é obrigatória para atendimentos concluídos.'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $sql = "
                UPDATE atendimentos
                SET status = :status, observacao = :observacao
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':status' => $status,
                ':observacao' => $observacao,
                ':id' => $id
            ]);

            echo json_encode([
                'mensagem' => 'Status atualizado com sucesso.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'erro' => 'Erro ao atualizar status.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function visualizar(): void
    {
        exigirAutenticacao();
        $usuario = usuarioAtual();
        require __DIR__ . '/../Views/atendimentos.php';
    }
}
