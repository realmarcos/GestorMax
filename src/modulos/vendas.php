<?php
require '../templates/header.php';
require '../auth_check.php';
require '../database/db_connect.php';

$mensagem_sucesso = "";
$mensagem_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_venda'])) {

    $id_cliente = (int) $_POST['id_cliente'];
    $valor_total_venda = (float) $_POST['valor_total_venda'];
    $id_usuario = (int) $_SESSION['id_usuario'];

    $produtos_ids = $_POST['produto_id'] ?? [];
    $produtos_qtd = $_POST['produto_qtd'] ?? [];
    $produtos_preco = $_POST['produto_preco'] ?? [];

        $conn->begin_transaction();

        try {
            $sql_venda = "INSERT INTO vendas (id_cliente, id_usuario, valor_total) VALUES (?, ?, ?)";
            $stmt_venda = $conn->prepare($sql_venda);
            $stmt_venda->bind_param("iid", $id_cliente, $id_usuario, $valor_total_venda);
            $stmt_venda->execute();

            $id_venda = $conn->insert_id;

            if ($id_venda == 0) {
                throw new Exception("Não foi possível registrar a venda.");
            }

            $sql_item = "INSERT INTO vendas_itens (id_venda, id_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
            $stmt_item = $conn->prepare($sql_item);


            foreach ($produtos_ids as $index => $id_produto) {
                $id_produto = (int) $id_produto;
                $quantidade = (int) $produtos_qtd[$index];
                $preco = (float) $produtos_preco[$index];

                $stmt_item->bind_param("iiid", $id_venda, $id_produto, $quantidade, $preco);
                $stmt_item->execute();

            }

            $conn->commit();
            $mensagem_sucesso = "Venda #" . $id_venda . " registrada com sucesso!";

        } catch (Exception $e) {
            $conn->rollback();
            $mensagem_erro = "Erro ao processar a venda: " . $e->getMessage();
        }
}

$result_clientes = $conn->query("SELECT id_cliente, nome FROM clientes ORDER BY nome ASC");

$produtos_disponiveis = [];
$result_produtos = $conn->query("SELECT id_produto, nome_produto, preco_venda, estoque FROM produtos WHERE estoque > 0 ORDER BY nome_produto ASC");

if ($result_produtos->num_rows > 0) {
    while ($row = $result_produtos->fetch_assoc()) {
        $produtos_disponiveis[$row['id_produto']] = $row;
    }
}

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Registrar Nova Venda</h1>

<?php
if (!empty($mensagem_sucesso)) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">' . $mensagem_sucesso . '</div>';
}
if (!empty($mensagem_erro)) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">' . $mensagem_erro . '</div>';
}
?>

<form id="form-venda" action="vendas.php" method="POST">

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold mb-4">1. Cliente</h2>
        <label for="id_cliente" class="block text-gray-700">Selecione o Cliente</label>
        <select id="id_cliente" name="id_cliente" required
            class="w-full md:w-1/2 px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            <option value="">-- Selecione --</option>
            <?php while ($cliente = $result_clientes->fetch_assoc()): ?>
                <option value="<?php echo $cliente['id_cliente']; ?>">
                    <?php echo htmlspecialchars($cliente['nome']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold mb-4">2. Adicionar Produtos</h2>
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-grow">
                <label for="select-produto" class="block text-gray-700">Produto</label>
                <select id="select-produto"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
                    <option value="">-- Selecione um produto --</option>
                    <?php foreach ($produtos_disponiveis as $produto): ?>
                        <option value="<?php echo $produto['id_produto']; ?>">
                            <?php echo htmlspecialchars($produto['nome_produto']); ?> (R$
                            <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?>) - Estoque:
                            <?php echo $produto['estoque']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="input-quantidade" class="block text-gray-700">Qtd.</label>
                <input type="number" id="input-quantidade" value="1" min="1"
                    class="w-24 px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <button type="button" id="btn-add-item"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 h-11">
                    Adicionar
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold mb-4">3. Itens da Venda</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white" id="tabela-itens">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left">Produto</th>
                        <th class="py-2 px-4 text-left">Qtd.</th>
                        <th class="py-2 px-4 text-left">Preço Unit.</th>
                        <th class="py-2 px-4 text-left">Subtotal</th>
                        <th class="py-2 px-4 text-left">Ação</th>
                    </tr>
                </thead>
                <tbody id="tbody-carrinho">
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500" id="carrinho-vazio">
                            Nenhum produto adicionado.
                        </td>
                    </tr>
                </tbody>
                <tfoot class="border-t-2 border-gray-300">
                    <tr>
                        <td colspan="3" class="py-3 px-4 text-right font-bold text-xl">TOTAL</td>
                        <td colspan="2" class="py-3 px-4 font-bold text-xl text-blue-600">
                            R$ <span id="span-total">0.00</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="text-right">
        <div id="hidden-inputs-carrinho"></div>
        <input type="hidden" name="valor_total_venda" id="hidden-total" value="0">

        <button type="submit" name="finalizar_venda" id="btn-finalizar"
            class="bg-green-600 text-white font-bold text-lg px-8 py-3 rounded shadow-lg hover:bg-green-700 disabled:bg-gray-400">
            Finalizar Venda
        </button>
    </div>

</form>


<script>
    const produtosDisponiveis = <?php echo json_encode($produtos_disponiveis); ?>;

    let carrinho = [];

    const selectProduto = document.getElementById('select-produto');
    const inputQtd = document.getElementById('input-quantidade');
    const btnAddItem = document.getElementById('btn-add-item');
    const tbodyCarrinho = document.getElementById('tbody-carrinho');
    const msgCarrinhoVazio = document.getElementById('carrinho-vazio');
    const spanTotal = document.getElementById('span-total');
    const hiddenInputsDiv = document.getElementById('hidden-inputs-carrinho');
    const hiddenTotal = document.getElementById('hidden-total');
    const btnFinalizar = document.getElementById('btn-finalizar');

    btnAddItem.addEventListener('click', adicionarItemAoCarrinho);

    selectProduto.addEventListener('change', () => {
        const id_produto = selectProduto.value;
        if (id_produto && produtosDisponiveis[id_produto]) {
            inputQtd.max = produtosDisponiveis[id_produto].estoque;
            if (parseInt(inputQtd.value) > parseInt(inputQtd.max)) {
                inputQtd.value = inputQtd.max;
            }
        }
    });


    function adicionarItemAoCarrinho() {
        const id_produto = selectProduto.value;
        let quantidade = parseInt(inputQtd.value);

        if (!id_produto) {
            alert("Por favor, selecione um produto.");
            return;
        }
        if (quantidade <= 0) {
            alert("A quantidade deve ser pelo menos 1.");
            return;
        }

        const produtoInfo = produtosDisponiveis[id_produto];

        const itemExistente = carrinho.find(item => item.id_produto == id_produto);

        let qtdTotal = quantidade;
        if (itemExistente) {
            qtdTotal = itemExistente.quantidade + quantidade;
        }

        if (qtdTotal > produtoInfo.estoque) {
            alert(`Estoque insuficiente. Você só pode adicionar mais ${produtoInfo.estoque - (itemExistente ? itemExistente.quantidade : 0)} unidades deste produto.`);
            return;
        }

        if (itemExistente) {
            itemExistente.quantidade = qtdTotal;
        } else {
            carrinho.push({
                id_produto: id_produto,
                nome: produtoInfo.nome_produto,
                quantidade: quantidade,
                preco_venda: parseFloat(produtoInfo.preco_venda),
            });
        }

        selectProduto.value = "";
        inputQtd.value = 1;

        atualizarVisualCarrinho();
    }

    function atualizarVisualCarrinho() {
        tbodyCarrinho.innerHTML = "";
        hiddenInputsDiv.innerHTML = "";

        let valorTotal = 0;

        if (carrinho.length === 0) {
            tbodyCarrinho.innerHTML = '<tr><td colspan="5" class="py-4 px-4 text-center text-gray-500" id="carrinho-vazio">Nenhum produto adicionado.</td></tr>';
        } else {
            carrinho.forEach((item, index) => {
                const subtotal = item.quantidade * item.preco_venda;
                valorTotal += subtotal;

                const tr = document.createElement('tr');
                tr.className = 'border-b';
                tr.innerHTML = `
                    <td class="py-2 px-4">${item.nome}</td>
                    <td class="py-2 px-4">${item.quantidade}</td>
                    <td class="py-2 px-4">R$ ${item.preco_venda.toFixed(2).replace('.', ',')}</td>
                    <td class="py-2 px-4">R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
                    <td class="py-2 px-4">
                        <button type="button" onclic="removerItemDoCarrinho(${item.id_produto})" class="text-red-500 hover:text-red-700">Remover</button>
                    </td>
                `;
                tbodyCarrinho.appendChild(tr);

                hiddenInputsDiv.innerHTML += `
                    <input type="hidden" name="produto_id[]" value="${item.id_produto}">
                    <input type="hidden" name="produto_qtd[]" value="${item.quantidade}">
                    <input type="hidden" name="produto_preco[]" value="${item.preco_venda}">
                `;
            });
            btnFinalizar.disabled = false;
        }

        spanTotal.textContent = valorTotal.toFixed(2).replace('.', ',');
        hiddenTotal.value = valorTotal.toFixed(2);
    }

    function removerItemDoCarrinho(id_produto) {
        carrinho = carrinho.filter(item => item.id_produto != id_produto);
        atualizarVisualCarrinho();
    }


</script>

<?php
$conn->close(); // Fecha a conexão com o banco
require '../templates/footer.php'; // Inclui o fechamento do </body> e </html>
?>