<?php

class tipoAtendimentoController
{
  private PDO $pdo;

  public function __construct()
  {
    require __DIR__ . '/../config/database.php';
    $this->pdo = $pdo;
  }

  private function jsonHeader(): void
  {
    header('Content-Type: application/json; charset=utf-8');
  }

  public function listar(): void
  {
    $this->jsonHeader();

    $sql = 'SELECT id, nome, descricao, status
                FROM tipos_atendimentos
                ORDER BY id DESC';

    $stmt = $this->pdo->query($sql);
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
      $tipos,
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
  }

  public function buscarPorId(): void
  {
    $this->jsonHeader();

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
      http_response_code(400);

      echo json_encode(
        ['erro' => 'ID inválido.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    $sql = 'SELECT id, nome, descricao, status
                FROM tipos_atendimentos
                WHERE id = :id';

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tipo) {
      http_response_code(404);

      echo json_encode(
        ['erro' => 'Tipo de atendimento não encontrado.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    echo json_encode(
      $tipo,
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
  }

  public function criar(): void
  {
    $this->jsonHeader();

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $status = strtolower(trim($_POST['status'] ?? 'ativo'));

    if ($nome === '' || $descricao === '') {
      http_response_code(400);

      echo json_encode(
        ['erro' => 'Nome e descrição são obrigatórios.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    if (strlen($nome) > 255) {
      http_response_code(400);

      echo json_encode(
        ['erro' => 'Nome excede o limite de 255 caracteres.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {
      http_response_code(400);

      echo json_encode(
        ['erro' => 'Status inválido.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    try {
      $sql = 'INSERT INTO tipos_atendimentos
                    (nome, descricao, status)
                    VALUES
                    (:nome, :descricao, :status)';

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':nome', $nome);
      $stmt->bindValue(':descricao', $descricao);
      $stmt->bindValue(':status', $status);

      $stmt->execute();

      http_response_code(201);

      echo json_encode(
        [
          'mensagem' => 'Tipo de atendimento cadastrado com sucesso.',
          'id' => $this->pdo->lastInsertId()
        ],
        JSON_UNESCAPED_UNICODE
      );
    } catch (PDOException $e) {

      if ($e->getCode() === '23000') {
        http_response_code(409);

        echo json_encode(
          ['erro' => 'Tipo de atendimento já cadastrado.'],
          JSON_UNESCAPED_UNICODE
        );

        return;
      }

      http_response_code(500);

      echo json_encode(
        ['erro' => 'Erro ao cadastrar tipo de atendimento.'],
        JSON_UNESCAPED_UNICODE
      );
    }
  }

  public function atualizar(): void
  {
    $this->jsonHeader();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $status = strtolower(trim($_POST['status'] ?? 'ativo'));

    if (!$id || $nome === '' || $descricao === '') {
      http_response_code(400);

      echo json_encode(
        ['erro' => 'ID, nome e descrição são obrigatórios.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {
      http_response_code(400);

      echo json_encode(
        ['erro' => 'Status inválido.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    try {
      $stmt = $this->pdo->prepare(
        'SELECT id
                 FROM tipos_atendimentos
                 WHERE id = :id'
      );

      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      if (!$stmt->fetch()) {
        http_response_code(404);

        echo json_encode(
          ['erro' => 'Tipo de atendimento não encontrado.'],
          JSON_UNESCAPED_UNICODE
        );

        return;
      }

      $sql = 'UPDATE tipos_atendimentos
                    SET nome = :nome,
                        descricao = :descricao,
                        status = :status
                    WHERE id = :id';

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':nome', $nome);
      $stmt->bindValue(':descricao', $descricao);
      $stmt->bindValue(':status', $status);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);

      $stmt->execute();

      echo json_encode(
        ['mensagem' => 'Tipo de atendimento atualizado com sucesso.'],
        JSON_UNESCAPED_UNICODE
      );
    } catch (PDOException $e) {

      if ($e->getCode() === '23000') {
        http_response_code(409);

        echo json_encode(
          ['erro' => 'Tipo de atendimento já cadastrado.'],
          JSON_UNESCAPED_UNICODE
        );

        return;
      }

      http_response_code(500);

      echo json_encode(
        ['erro' => 'Erro ao atualizar tipo de atendimento.'],
        JSON_UNESCAPED_UNICODE
      );
    }
  }

  public function excluir(): void
  {
    $this->jsonHeader();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
      http_response_code(400);

      echo json_encode(
        ['erro' => 'ID inválido.'],
        JSON_UNESCAPED_UNICODE
      );

      return;
    }

    try {
      $stmt = $this->pdo->prepare(
        'SELECT id
                 FROM tipos_atendimentos
                 WHERE id = :id'
      );

      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      if (!$stmt->fetch()) {
        http_response_code(404);

        echo json_encode(
          ['erro' => 'Tipo de atendimento não encontrado.'],
          JSON_UNESCAPED_UNICODE
        );

        return;
      }

      $sql = 'UPDATE tipos_atendimentos
                    SET status = :status
                    WHERE id = :id';

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':status', 'inativo');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);

      $stmt->execute();

      echo json_encode(
        ['mensagem' => 'Tipo de atendimento inativado com sucesso.'],
        JSON_UNESCAPED_UNICODE
      );
    } catch (PDOException $e) {

      http_response_code(500);

      echo json_encode(
        ['erro' => 'Erro ao inativar tipo de atendimento.'],
        JSON_UNESCAPED_UNICODE
      );
    }
  }
}
