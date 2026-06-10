<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AtendLab</title>
    <link rel="stylesheet" href="./assets/css/home.css">
    <link rel="stylesheet" href="./assets/css/login-modal.css">
</head>

<body>
    <div class="container">
        <img src="./assets/img/book-open-text.svg" width="64" height="64" class="svg-color" />
        <h1 class="title">Bem vindo ao AtendLab!</h1>
        <button class="access-button">Acessar painel de gestão</button>
    </div>
    <div class="backdrop" id="loginModal">
        <div class="modal-container">
            <form action="autenticacao.php" method="POST">
                <div class="modal-header">
                    <p class="modal-title">Acesse a sua conta</p>
                    <button type="button" class="modal-close-button" id="closeModalButton" aria-label="Fechar modal">
                        <img src="./assets/img/x.svg" alt="Fechar" />
                    </button>
                </div>
                <label class="modal-label">Seu email de acesso</label>
                <input name="email" placeholder="Insira seu email" type="email" class="modal-input" required />
                <label class="modal-label">Sua senha de acesso</label>
                <input name="senha" placeholder="Insira sua senha" type="password" class="modal-input" required />
                <button type="submit" class="modal-button">Fazer Login</button>
            </form>
        </div>
    </div>
    <script src="./assets/js/toggle-modal.js"></script>
</body>

</html>