<?php

if (!isset($_SESSION['id_usuario'])) {
    // Se não estiver logado, redireciona para o login
    header("Location: /index.php"); // Ajuste o caminho se necessário
    exit;
}
?>