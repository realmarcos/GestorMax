<?php
// Inicia a sessão em todas as páginas que usam o header
session_start();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestor Max</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

  <?php

  if (isset($_SESSION['id_usuario'])):
  ?>
    <nav class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <span class="font-bold text-xl text-blue-600">Gestor Max</span>
          </div>
          <div class="flex items-center space-x-4">
            <a href="dashboard.php" class="text-gray-600 hover:text-blue-500">Dashboard</a>
            <a href="/modulos/vendas.php" class="text-gray-600 hover:text-blue-500">Nova Venda</a>
            <a href="/modulos/clientes.php" class="text-gray-600 hover:text-blue-500">Clientes</a>
            <a href="/modulos/produtos.php" class="text-gray-600 hover:text-blue-500">Produtos</a>
            <a href="/modulos/relatorios.php" class="text-gray-600 hover:text-blue-500">Relatórios</a>
            <a href="/modulos/usuarios.php" class="text-gray-600 hover:text-blue-500">Usuários</a>
            <span class="text-gray-500">| Olá, <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?></span>
            <a href="logout.php" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Sair</a>
          </div>
        </div>
      </div>
    </nav>
  <?php endif; ?>


