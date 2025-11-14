<?php
// 1. Inclusões e Verificações Essenciais
require '../templates/header.php'; // Inclui o <head>, <body>, e o menu (e já dá session_start())
require '../auth_check.php';      // Garante que o usuário está logado
require '../database/db_connect.php';      // Conecta ao banco de dados

// 2. Consulta SQL para buscar as vendas (A "inteligência" do relatório)

// Esta consulta une as tabelas para obter os nomes do cliente e do vendedor,
// em vez de apenas os IDs.
// Ordenamos por data_venda DESC (descendente) para mostrar as mais novas primeiro.
$sql = "
    SELECT 
        v.id_venda,
        v.data_venda,
        v.valor_total,
        c.nome AS nome_cliente,
        u.nome AS nome_vendedor
    FROM 
        vendas AS v
    JOIN 
        clientes AS c ON v.id_cliente = c.id_cliente
    JOIN 
        usuarios AS u ON v.id_usuario = u.id_usuario
    ORDER BY 
        v.data_venda DESC;
";

$result_vendas = $conn->query($sql);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Relatório de Vendas</h1>

<div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
    <h2 class="text-2xl font-semibold mb-4">Vendas Realizadas</h2>
    
    <table class="min-w-full bg-white">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-4 text-left">Nº Venda</th>
                <th class="py-2 px-4 text-left">Data e Hora</th>
                <th class="py-2 px-4 text-left">Cliente</th>
                <th class="py-2 px-4 text-left">Vendedor</th>
                <th class="py-2 px-4 text-left">Valor Total (R$)</th>
                </tr>
        </thead>
        <tbody>
            <?php if ($result_vendas && $result_vendas->num_rows > 0): ?>
                <?php while($venda = $result_vendas->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4 font-medium">
                        #<?php echo $venda['id_venda']; ?>
                    </td>
                    <td class="py-2 px-4">
                        <?php 
                        // Formata a data e hora do padrão MySQL para o padrão BR
                        $data = new DateTime($venda['data_venda']);
                        echo $data->format('d/m/Y H:i'); 
                        ?>
                    </td>
                    <td class="py-2 px-4">
                        <?php echo htmlspecialchars($venda['nome_cliente']); ?>
                    </td>
                    <td class="py-2 px-4">
                        <?php echo htmlspecialchars($venda['nome_vendedor']); ?>
                    </td>
                    <td class="py-2 px-4 font-medium text-green-600">
                        <?php 
                        // Formata o valor monetário
                        echo "R$ " . number_format($venda['valor_total'], 2, ',', '.'); 
                        ?>
                    </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                        Nenhuma venda registrada até o momento.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// 5. Inclusão do Footer e Fechamento da Conexão
$conn->close();
require '../templates/footer.php'; 
?>