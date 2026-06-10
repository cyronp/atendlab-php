<?php

class atendimentoController {
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id, pessoa_id, usuario_id, tipo_atendimento_id, data_atendimento, hora_atendimento, descricao, observacao, status FROM atendimentos ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $atendimento = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(
            $atendimento,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if(!$id){
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT id, pessoa_id, usuario_id, tipo_atendimento_id, data_atendimento, hora_atendimento, descricao, observacao, status FROM atendimentos WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento){
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado.']);
            return;
        }

        echo json_encode(
            $atendimento,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function criar(): void

    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoa_id = $_POST['pessoa_id'] ?? '';
        $usuario_id = $_POST['usuario_id'] ?? '';
        $tipo_atendimento_id = $_POST['tipo_atendimento_id'] ?? '';
        $data_atendimento = $_POST['data_atendimento'] ?? '';
        $hora_atendimento = $_POST['hora_atendimento'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        $status = $_POST['status'] ?? 'aberto';

        if ($pessoa_id === '' || $usuario_id === ''|| $tipo_atendimento_id === ''){
            http_response_code(400);
            echo json_encode(['erro' => 'Relação com pessoa, usuario e tipo de atendimento são obrigatórios.']);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)){
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (pessoa_id, usuario_id, tipo_atendimento_id, data_atendimento, hora_atendimento, descricao, observacao, status) VALUES (:pessoa_id, :usuario_id, :tipo_atendimento_id, :data_atendimento, :hora_atendimento, :descricao, :observacao, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id', $pessoa_id);
            $stmt->bindValue(':usuario_id', $usuario_id);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':hora_atendimento', $hora_atendimento);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':observacao', $observacao);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);

            echo json_encode([
                'messagem' => 'Atendimento cadastrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (\PDOException $e){
            return;
        }
        http_response_code(500);

        echo json_encode([
            'erro' => 'Erro ao cadastrar atendimento.'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $pessoa_id = $_POST['pessoa_id'] ?? '';
        $usuario_id = $_POST['usuario_id'] ?? '';
        $tipo_atendimento_id = $_POST['tipo_atendimento_id'] ?? '';
        $data_atendimento = $_POST['data_atendimento'] ?? '';
        $hora_atendimento = $_POST['hora_atendimento'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        $status = $_POST['status'] ?? 'aberto';
        
        if (!$id || $pessoa_id === '' || $usuario_id === '' || $tipo_atendimento_id === ''){
            http_response_code(400);
            echo json_encode(['erro' => 'ID e relação com pessoa, usuario e tipo de atendimento são obrigatórios.']);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)){
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT id from atendimentos WHERE id = :id'
            );

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()){
                http_response_code(404);
                echo json_encode([
                    'erro' => 'Atendimento não encontrado.'
                ]);
                return;
            }

            $sql = 'UPDATE atendimentos SET pessoa_id = :pessoa_id, usuario_id = :usuario_id, tipo_atendimento_id = :tipo_atendimento_id, data_atendimento = :data_atendimento, hora_atendimento = :hora_atendimento, descricao = :descricao, observacao = :observacao, status = :status WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id', $pessoa_id);
            $stmt->bindValue(':usuario_id', $usuario_id);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':hora_atendimento', $hora_atendimento);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':observacao', $observacao);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Atendimento adicionado com sucesso.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (\PDOException $e){
            return;
        }
        http_response_code(500);

        echo json_encode([
            'erro' => 'Erro ao atualizar atendimento.'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function excluir(): void
    {
      header('Content-Type: application/json; charset=utf-8');
  
      $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  
      if (!$id) {
        http_response_code(400);
  
        echo json_encode([
          'erro' => 'ID inválido.'
        ], JSON_UNESCAPED_UNICODE);
  
        return;
      }
  
      try {
        $sql = 'UPDATE atendimentos SET status = :status WHERE id = :id';
  
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', 'concluido');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
  
        if ($stmt->rowCount() === 0) {
          http_response_code(404);
  
          echo json_encode([
            'erro' => 'Atendimento não encontrado.'
          ], JSON_UNESCAPED_UNICODE);
          return;
        }
  
        echo json_encode([
          'mensagem' => 'Atendimento concluído/inativado com sucesso.'
        ], JSON_UNESCAPED_UNICODE);
      } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode([
          'erro' => 'Erro ao concluir/inativar atendimento.'
        ], JSON_UNESCAPED_UNICODE);
      }
    }
}