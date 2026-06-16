<?php
if (basename($_SERVER['SCRIPT_NAME']) === 'index.php' && strpos($_SERVER['SCRIPT_NAME'], '/public/') === false) {
    header('Location: public/index.php');
    exit;
}
$pathPrefix = (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false) ? '../' : './';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AtendLab</title>
    <link rel="stylesheet" href="<?= $pathPrefix ?>assets/css/home.css">
    <link rel="stylesheet" href="<?= $pathPrefix ?>assets/css/login-modal.css">
</head>

<body>
    <div class="container">
        <img src="<?= $pathPrefix ?>assets/img/book-open-text.svg" width="64" height="64" class="svg-color" />
        <h1 class="title">Bem vindo ao AtendLab!</h1>
        <button class="access-button">Acessar painel de gestão</button>
    </div>
    <div class="backdrop <?= (isset($erro) || isset($mensagem)) ? 'visible' : '' ?>" id="loginModal">
        <div class="modal-container">
            <form action="?controller=auth&action=entrar" method="POST">
                <div class="modal-header">
                    <p class="modal-title">Acesse a sua conta</p>
                    <button type="button" class="modal-close-button" id="closeModalButton" aria-label="Fechar modal">
                        <img src="<?= $pathPrefix ?>assets/img/x.svg" alt="Fechar" />
                    </button>
                </div>
                <?php if (isset($erro) && $erro): ?>
                    <div style="background-color: oklch(90% 0.1 15); color: oklch(40% 0.15 15); padding: 0.75rem; border-radius: 0.5rem; font-size: 0.9rem; text-align: center; font-weight: 500;">
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($mensagem) && $mensagem): ?>
                    <div style="background-color: oklch(90% 0.1 140); color: oklch(40% 0.15 140); padding: 0.75rem; border-radius: 0.5rem; font-size: 0.9rem; text-align: center; font-weight: 500;">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>
                <label class="modal-label">Seu email de acesso</label>
                <input name="email" placeholder="Insira seu email" type="email" class="modal-input" required />
                <label class="modal-label">Sua senha de acesso</label>
                <input name="senha" placeholder="Insira sua senha" type="password" class="modal-input" required />
                <button type="submit" class="modal-button">Fazer Login</button>
            </form>
        </div>
    </div>
    <script src="<?= $pathPrefix ?>assets/js/toggle-modal.js"></script>
</body>

</html>