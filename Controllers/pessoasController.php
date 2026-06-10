<?php

class pessoasController
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

    $sql = 'SELECT id, nome, cpf, telefone, email, curso, periodo, status, criado_em FROM pessoas ORDER BY id DESC';

    $stmt = $this->pdo->query($sql);
    $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
      $pessoas,
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
  }

  public function buscarPorId(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
      http_response_code(400);
      echo json_encode(['erro' => 'ID inválido.']);
      return;
    }

    $sql = 'SELECT id, nome, cpf, telefone, email, curso, periodo, status, criado_em FROM pessoas WHERE id = :id';

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pessoa) {
      http_response_code(404);
      echo json_encode(['erro' => 'Pessoa não encontrada.']);
      return;
    }

    echo json_encode(
      $pessoa,
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
  }

  public function criar(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $curso = ($_POST['curso'] ?? '');
    $periodo = ($_POST['periodo'] ?? '');
    $status = ($_POST['status'] ?? 'ativo');

    if ($nome === '' || $email === '' || $cpf === '' || $telefone === '') {
      http_response_code(400);
      echo json_encode(['erro' => 'Nome, e-mail, cpf, telefone são obrigatórios.']);
      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      http_response_code(400);
      echo json_encode(['erro' => 'E-maail inválido.']);
      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {
      http_response_code(400);
      echo json_encode(['erro' => 'Status inválido.']);
      return;
    }

    try {
      $sql = 'INSERT INTO pessoas (nome, email, cpf, telefone, curso, periodo, status) VALUES (:nome, :email, :cpf, :telefone, :curso, :periodo, :status)';

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':nome', $nome);
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':cpf', $cpf);
      $stmt->bindValue(':telefone', $telefone);
      $stmt->bindValue(':curso', $curso);
      $stmt->bindValue(':periodo', $periodo);
      $stmt->bindValue(':status', $status);
      $stmt->execute();

      http_response_code(201);

      echo json_encode([
        'messagem' => 'Pessoa cadastrada com sucesso.',
        'id' => $this->pdo->lastInsertId()
      ], JSON_UNESCAPED_UNICODE);
    } catch (\PDOException $e) {
      if ($e->getCode() === '23000') {
        http_response_code(409);

        echo json_encode([
          'erro' => 'E-mail já cadastrado.'
        ], JSON_UNESCAPED_UNICODE);

        return;
      }
      http_response_code(500);

      echo json_encode([
        'erro' => 'Erro ao cadastrar pessoa.'
      ], JSON_UNESCAPED_UNICODE);
    }
  }

  public function atualizar(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $curso = ($_POST['curso'] ?? '');
    $periodo = ($_POST['periodo'] ?? '');
    $status = ($_POST['status'] ?? 'ativo');

    if (!$id || $nome === '' || $email === '' || $cpf === '' || $telefone === '') {
      http_response_code(400);
      echo json_encode(['erro' => 'ID, nome, e-mail, cpf, telefone são obrigatórios.']);
      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      http_response_code(400);
      echo json_encode(['erro' => 'E-maail inválido.']);
      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {
      http_response_code(400);
      echo json_encode(['erro' => 'Status inválido.']);
      return;
    }

    try {
      $stmt = $this->pdo->prepare(
        'SELECT id from pessoas WHERE id = :id'
      );

      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode([
          'erro' => 'Pessoa não encontrada.'
        ]);
        return;
      }

      $sql = 'UPDATE pessoas SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, curso = :curso,  periodo = :periodo, status = :status WHERE id = :id';

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':nome', $nome);
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':cpf', $cpf);
      $stmt->bindValue(':telefone', $telefone);
      $stmt->bindValue(':curso', $curso);
      $stmt->bindValue(':periodo', $periodo);
      $stmt->bindValue(':status', $status);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);

      $stmt->execute();

      echo json_encode([
        'mensagem' => 'Pessoa atualizada com sucesso.'
      ], JSON_UNESCAPED_UNICODE);
    } catch (\PDOException $e) {

      if ($e->getCode() === '23000') {
        http_response_code(409);

        echo json_encode([
          'erro' => 'E-mail já cadastrado.'
        ], JSON_UNESCAPED_UNICODE);
        return;
      }
      http_response_code(500);

      echo json_encode([
        'erro' => 'Erro ao atualizar pessoa.'
      ], JSON_UNESCAPED_UNICODE);
    }
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
      $sql = 'UPDATE pessoas SET status = :status WHERE id = :id';

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':status', 'inativo');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() === 0) {
        http_response_code(404);

        echo json_encode([
          'erro' => 'Pessoa não encontado.'
        ], JSON_UNESCAPED_UNICODE);
        return;
      }

      echo json_encode([
        'mensagem' => 'Pessoa inativa com sucesso.'
      ], JSON_UNESCAPED_UNICODE);
    } catch (\PDOException $e) {
      http_response_code(500);
      echo json_encode([
        'erro' => 'Erro ao inativar pessoa.'
      ], JSON_UNESCAPED_UNICODE);
    }
  }
}
