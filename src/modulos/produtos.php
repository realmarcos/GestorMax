<?php

require '../templates/header.php';
require '../auth_check.php';
require '../database/db_connect.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar_produto'])) {

    $nome_produto = $conn->real_escape_string($_POST['nome_produto']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $preco_venda = $_POST['preco_venda']; 
    $estoque = $_POST['estoque'];        

    if (!is_numeric($preco_venda) || !is_numeric($estoque)) {
        $mensagem = "Erro: Preço e Estoque devem ser números.";
    } else {
        $sql_insert = "INSERT INTO produtos (nome_produto, descricao, preco_venda, estoque) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);

        $stmt->bind_param("ssdi", $nome_produto, $descricao, $preco_venda, $estoque);

        if ($stmt->execute()) {
            header("Location: produtos.php?sucesso=1");
            exit;
        } else {
            $mensagem = "Erro ao cadastrar produto: " . $stmt->error;
        }
        $stmt->close();
    }
}

$sql_select = "SELECT id_produto, nome_produto, preco_venda, estoque FROM produtos ORDER BY nome_produto ASC";
$result_produtos = $conn->query($sql_select);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Gestão de Produtos</h1>

<?php
if (isset($_GET['sucesso'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">Produto cadastrado com sucesso!</div>';
}
if (!empty($mensagem) && !isset($_GET['sucesso'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">' . $mensagem . '</div>';
}
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4">Cadastrar Novo Produto</h2>

    <form action="produtos.php" method="POST">
        <div class="mb-4">
            <label for="nome_produto" class="block text-gray-700">Nome do Produto</label>
            <input type="text" id="nome_produto" name="nome_produto" required class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="descricao" class="block text-gray-700">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="preco_venda" class="block text-gray-700">Preço de Venda (R$)</label>
                <input type="number" id="preco_venda" name="preco_venda" step="0.01" min="0" required class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label for="estoque" class="block text-gray-700">Estoque (Unidades)</label>
                <input type="number" id="estoque" name="estoque" step="1" min="0" required class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <button type="submit" name="cadastrar_produto" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Salvar Produto
        </button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
    <h2 class="text-2xl font-semibold mb-4">Produtos Cadastrados</h2>
    <table class="min-w-full bg-white">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-4 text-left">Nome do Produto</th>
                <th class="py-2 px-4 text-left">Preço (R$)</th>
                <th class="py-2 px-4 text-left">Estoque</th>
                <th class="py-2 px-4 text-left">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_produtos->num_rows > 0): ?>
                <?php while ($produto = $result_produtos->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($produto['nome_produto']); ?></td>
                        <td class="py-2 px-4">
                            <?php
                            echo "R$ " . number_format($produto['preco_venda'], 2, ',', '.');
                            ?>
                        </td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($produto['estoque']); ?></td>
                        <td class="py-2 px-4">
                            <a href="#" class="text-yellow-500 hover:text-yellow-700 mx-2">Editar</a>
                            <a href="#" class="text-red-500 hover:text-red-700 mx-2">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="py-4 px-4 text-center text-gray-500">Nenhum produto cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
require '../templates/footer.php';
?>