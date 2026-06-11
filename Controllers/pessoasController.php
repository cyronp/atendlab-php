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

    try {

      $sql = '
        SELECT id, nome, cpf, telefone, email, curso, periodo, status, criado_em
        FROM pessoas
        ORDER BY id DESC
      ';

      $stmt = $this->pdo->query($sql);

      echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
      );
    } catch (PDOException $e) {

      error_log($e->getMessage());

      http_response_code(500);

      echo json_encode([
        'erro' => 'Erro ao listar pessoas.'
      ], JSON_UNESCAPED_UNICODE);
    }
  }

  public function buscarPorId(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null) {

      http_response_code(400);

      echo json_encode(['erro' => 'ID inválido.']);
      return;
    }

    try {

      $sql = '
        SELECT id, nome, cpf, telefone, email, curso, periodo, status, criado_em
        FROM pessoas
        WHERE id = :id
      ';

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$pessoa) {

        http_response_code(404);

        echo json_encode(['erro' => 'Pessoa não encontrada.']);
        return;
      }

      echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {

      error_log($e->getMessage());

      http_response_code(500);

      echo json_encode(['erro' => 'Erro ao buscar pessoa.']);
    }
  }

  public function criar(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

    $curso = trim($_POST['curso'] ?? '');
    $periodo = trim($_POST['periodo'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if ($nome === '' || $email === '' || $cpf === '' || $telefone === '') {

      http_response_code(400);

      echo json_encode(['erro' => 'Nome, e-mail, CPF e telefone são obrigatórios.']);
      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

      http_response_code(400);

      echo json_encode(['erro' => 'E-mail inválido.']);
      return;
    }

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {

      http_response_code(400);

      echo json_encode(['erro' => 'Telefone inválido.']);
      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {

      http_response_code(400);

      echo json_encode(['erro' => 'Status inválido.']);
      return;
    }

    try {

      $sql = '
        INSERT INTO pessoas (nome, cpf, telefone, email, curso, periodo, status)
        VALUES (:nome, :cpf, :telefone, :email, :curso, :periodo, :status)
      ';

      $stmt = $this->pdo->prepare($sql);

      $stmt->execute([
        ':nome' => $nome,
        ':cpf' => $cpf,
        ':telefone' => $telefone,
        ':email' => $email,
        ':curso' => $curso,
        ':periodo' => $periodo,
        ':status' => $status
      ]);

      http_response_code(201);

      echo json_encode([
        'mensagem' => 'Pessoa cadastrada com sucesso.',
        'id' => $this->pdo->lastInsertId()
      ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {

      error_log($e->getMessage());

      if ($e->errorInfo[1] == 1062) {

        http_response_code(409);

        echo json_encode(['erro' => 'CPF ou e-mail já cadastrado.']);
        return;
      }

      http_response_code(500);

      echo json_encode(['erro' => 'Erro ao cadastrar pessoa.']);
    }
  }

  public function atualizar(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

    $curso = trim($_POST['curso'] ?? '');
    $periodo = trim($_POST['periodo'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if ($id === false || $id === null || $nome === '' || $email === '' || $cpf === '' || $telefone === '') {

      http_response_code(400);

      echo json_encode(['erro' => 'ID, nome, e-mail, CPF e telefone são obrigatórios.']);
      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

      http_response_code(400);

      echo json_encode(['erro' => 'E-mail inválido.']);
      return;
    }

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {

      http_response_code(400);

      echo json_encode(['erro' => 'Telefone inválido.']);
      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {

      http_response_code(400);

      echo json_encode(['erro' => 'Status inválido.']);
      return;
    }

    try {

      $sql = '
        UPDATE pessoas
        SET nome = :nome,
            cpf = :cpf,
            telefone = :telefone,
            email = :email,
            curso = :curso,
            periodo = :periodo,
            status = :status
        WHERE id = :id
      ';

      $stmt = $this->pdo->prepare($sql);

      $stmt->execute([
        ':id' => $id,
        ':nome' => $nome,
        ':cpf' => $cpf,
        ':telefone' => $telefone,
        ':email' => $email,
        ':curso' => $curso,
        ':periodo' => $periodo,
        ':status' => $status
      ]);

      if ($stmt->rowCount() === 0) {

        echo json_encode(['mensagem' => 'Nenhuma alteração realizada.']);
        return;
      }

      echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso.']);
    } catch (PDOException $e) {

      error_log($e->getMessage());

      if ($e->errorInfo[1] == 1062) {

        http_response_code(409);

        echo json_encode(['erro' => 'CPF ou e-mail já cadastrado.']);
        return;
      }

      http_response_code(500);

      echo json_encode(['erro' => 'Erro ao atualizar pessoa.']);
    }
  }

  public function excluir(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null) {

      http_response_code(400);

      echo json_encode(['erro' => 'ID inválido.']);
      return;
    }

    try {

      $sql = '
        UPDATE pessoas
        SET status = :status
        WHERE id = :id
      ';

      $stmt = $this->pdo->prepare($sql);

      $stmt->execute([
        ':status' => 'inativo',
        ':id' => $id
      ]);

      if ($stmt->rowCount() === 0) {

        http_response_code(404);

        echo json_encode(['erro' => 'Pessoa não encontrada.']);
        return;
      }

      echo json_encode(['mensagem' => 'Pessoa inativada com sucesso.']);
    } catch (PDOException $e) {

      error_log($e->getMessage());

      http_response_code(500);

      echo json_encode(['erro' => 'Erro ao inativar pessoa.']);
    }
  }
}
