<?php
session_start();
require './database/db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $senha_digitada = $_POST['senha'];

    $sql = "SELECT id_usuario, nome, email, senha FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verifica a senha usando password_verify()
        if (password_verify($senha_digitada, $user['senha'])) {
            // Senha correta! Inicia a sessão
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nome_usuario'] = $user['nome'];
            $_SESSION['email_usuario'] = $user['email'];

            header("Location: dashboard.php");
            exit;
        }
    }

    // Se chegou aqui, o login falhou
    header("Location: index.php?erro=1");
    exit;
} else {
    header("Location: index.php");
    exit;
}
