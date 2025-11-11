<?php
// 1. Inclusões e Verificações Essenciais
require 'templates/header.php'; // Inclui o <head>, <body>, e o menu (e já dá session_start())
require 'auth_check.php';      // Garante que o usuário está logado
require 'database/db_connect.php';      // Conecta ao banco de dados

// -----------------------------------------------------------------
// 2. BUSCAR ESTATÍSTICAS (KPIs) PARA O PAINEL
// -----------------------------------------------------------------

// 1. Total de Clientes
$result_clientes = $conn->query("SELECT COUNT(*) as total_clientes FROM clientes");
$total_clientes = $result_clientes->fetch_assoc()['total_clientes'] ?? 0;

// 2. Total de Produtos
$result_produtos = $conn->query("SELECT COUNT(*) as total_produtos FROM produtos");
$total_produtos = $result_produtos->fetch_assoc()['total_produtos'] ?? 0;

// 3. Total de Vendas (Número de transações)
$result_vendas = $conn->query("SELECT COUNT(*) as total_vendas FROM vendas");
$total_vendas = $result_vendas->fetch_assoc()['total_vendas'] ?? 0;

// 4. Faturamento Total
$result_faturamento = $conn->query("SELECT SUM(valor_total) as faturamento_total FROM vendas");
// '?? 0' garante que, se não houver vendas, ele mostre 0 e não dê erro
$faturamento_total = $result_faturamento->fetch_assoc()['faturamento_total'] ?? 0;

?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">
        Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>!
    </h1>
    <p class="text-gray-600">Aqui está um resumo do seu negócio.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-sm font-medium text-gray-500 uppercase">Faturamento Total</h3>
        <p class="text-3xl font-semibold text-gray-900 mt-2">
            R$ <?php echo number_format($faturamento_total, 2, ',', '.'); ?>
        </p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-sm font-medium text-gray-500 uppercase">Vendas Realizadas</h3>
        <p class="text-3xl font-semibold text-gray-900 mt-2">
            <?php echo $total_vendas; ?>
        </p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-sm font-medium text-gray-500 uppercase">Clientes Cadastrados</h3>
        <p class="text-3xl font-semibold text-gray-900 mt-2">
            <?php echo $total_clientes; ?>
        </p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-sm font-medium text-gray-500 uppercase">Produtos em Catálogo</h3>
        <p class="text-3xl font-semibold text-gray-900 mt-2">
            <?php echo $total_produtos; ?>
        </p>
    </div>
</div>

<div class="mb-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Ações Rápidas</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <a href="modulos/vendas.php" class="bg-blue-600 text-white p-6 rounded-lg shadow-md hover:bg-blue-700 transition-colors flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold mb-2">Nova Venda</h3>
                <p>Registrar uma nova venda e faturar.</p>
            </div>
            <span class="mt-4 font-semibold">Ir &rarr;</span>
        </a>

        <a href="modulos/clientes.php" class="bg-white text-gray-800 p-6 rounded-lg shadow-md hover:bg-gray-50 transition-colors flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold mb-2">Gerenciar Clientes</h3>
                <p>Cadastrar ou editar clientes.</p>
            </div>
            <span class="mt-4 font-semibold text-blue-600">Ir &rarr;</span>
        </a>

        <a href="modulos/produtos.php" class="bg-white text-gray-800 p-6 rounded-lg shadow-md hover:bg-gray-50 transition-colors flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold mb-2">Gerenciar Produtos</h3>
                <p>Adicionar ou editar estoque e preços.</p>
            </div>
            <span class="mt-4 font-semibold text-blue-600">Ir &rarr;</span>
        </a>

        <a href="modulos/relatorios.php" class="bg-white text-gray-800 p-6 rounded-lg shadow-md hover:bg-gray-50 transition-colors flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold mb-2">Relatórios</h3>
                <p>Visualizar histórico de vendas.</p>
            </div>
            <span class="mt-4 font-semibold text-blue-600">Ir &rarr;</span>
        </a>

    </div>
</div>


<?php


$conn->close(); 
require 'templates/footer.php'; 
?>