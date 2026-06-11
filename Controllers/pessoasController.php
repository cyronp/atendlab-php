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
                SELECT
                    id,
                    nome,
                    cpf,
                    telefone,
                    email,
                    curso,
                    periodo,
                    status,
                    criado_em
                FROM pessoas
                ORDER BY id DESC
            ';

      $stmt = $this->pdo->query($sql);

      echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
      );
    } catch (PDOException $e) {

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

    if (!$id) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'ID inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    try {

      $sql = '
                SELECT
                    id,
                    nome,
                    cpf,
                    telefone,
                    email,
                    curso,
                    periodo,
                    status,
                    criado_em
                FROM pessoas
                WHERE id = :id
            ';

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$pessoa) {

        http_response_code(404);

        echo json_encode([
          'erro' => 'Pessoa não encontrada.'
        ], JSON_UNESCAPED_UNICODE);

        return;
      }

      echo json_encode(
        $pessoa,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
      );
    } catch (PDOException $e) {

      http_response_code(500);

      echo json_encode([
        'erro' => 'Erro ao buscar pessoa.'
      ], JSON_UNESCAPED_UNICODE);
    }
  }

  public function criar(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

    $curso = trim($_POST['curso'] ?? '');
    $periodo = trim($_POST['periodo'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if (
      $nome === '' ||
      $email === '' ||
      $cpf === '' ||
      $telefone === ''
    ) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'Nome, e-mail, CPF e telefone são obrigatórios.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'E-mail inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (strlen($cpf) !== 11) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'CPF inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'Telefone inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'Status inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    try {

      $sql = '
                INSERT INTO pessoas (
                    nome,
                    cpf,
                    telefone,
                    email,
                    curso,
                    periodo,
                    status
                )
                VALUES (
                    :nome,
                    :cpf,
                    :telefone,
                    :email,
                    :curso,
                    :periodo,
                    :status
                )
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

      if ($e->getCode() === '23000') {

        http_response_code(409);

        echo json_encode([
          'erro' => 'CPF ou e-mail já cadastrado.'
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
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

    $curso = trim($_POST['curso'] ?? '');
    $periodo = trim($_POST['periodo'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if (
      !$id ||
      $nome === '' ||
      $email === '' ||
      $cpf === '' ||
      $telefone === ''
    ) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'ID, nome, e-mail, CPF e telefone são obrigatórios.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'E-mail inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (strlen($cpf) !== 11) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'CPF inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'Telefone inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (!in_array($status, ['ativo', 'inativo'], true)) {

      http_response_code(400);

      echo json_encode([
        'erro' => 'Status inválido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    try {

      $stmt = $this->pdo->prepare(
        'SELECT id FROM pessoas WHERE id = :id'
      );

      $stmt->execute([
        ':id' => $id
      ]);

      if (!$stmt->fetch()) {

        http_response_code(404);

        echo json_encode([
          'erro' => 'Pessoa não encontrada.'
        ], JSON_UNESCAPED_UNICODE);

        return;
      }

      $sql = '
                UPDATE pessoas
                SET
                    nome = :nome,
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

      echo json_encode([
        'mensagem' => 'Pessoa atualizada com sucesso.'
      ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {

      if ($e->getCode() === '23000') {

        http_response_code(409);

        echo json_encode([
          'erro' => 'CPF ou e-mail já cadastrado.'
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

      $stmt = $this->pdo->prepare(
        'SELECT id FROM pessoas WHERE id = :id'
      );

      $stmt->execute([
        ':id' => $id
      ]);

      if (!$stmt->fetch()) {

        http_response_code(404);

        echo json_encode([
          'erro' => 'Pessoa não encontrada.'
        ], JSON_UNESCAPED_UNICODE);

        return;
      }

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

      echo json_encode([
        'mensagem' => 'Pessoa inativada com sucesso.'
      ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {

      http_response_code(500);

      echo json_encode([
        'erro' => 'Erro ao inativar pessoa.'
      ], JSON_UNESCAPED_UNICODE);
    }
  }
}
