<?php

require '../templates/header.php'; 
require '../auth_check.php';      
require '../database/db_connect.php';      

$mensagem = ""; 


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar_cliente'])) {

    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefone = $conn->real_escape_string($_POST['telefone']);
    $endereco = $conn->real_escape_string($_POST['endereco']);

    $sql_insert = "INSERT INTO clientes (nome, email, telefone, endereco) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);

    $stmt->bind_param("ssss", $nome, $email, $telefone, $endereco);

    if ($stmt->execute()) {
        $mensagem = "Cliente cadastrado com sucesso!";
        header("Location: clientes.php?sucesso=1");
        exit;
    } else {
        $mensagem = "Erro ao cadastrar cliente: " . $stmt->error;
    }
    $stmt->close();
}

$sql_select = "SELECT id_cliente, nome, email, telefone, endereco FROM clientes ORDER BY nome ASC";
$result_clientes = $conn->query($sql_select);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Gestão de Clientes</h1>

<?php

if (isset($_GET['sucesso'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">Cliente cadastrado com sucesso!</div>';
}
if (!empty($mensagem) && !isset($_GET['sucesso'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">' . $mensagem . '</div>';
}
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4">Cadastrar Novo Cliente</h2>

    <form action="clientes.php" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="nome" class="block text-gray-700">Nome Completo</label>
                <input type="text" id="nome" name="nome" required class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="telefone" class="block text-gray-700">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label for="endereco" class="block text-gray-700">Endereço</label>
                <input type="text" id="endereco" name="endereco" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <button type="submit" name="cadastrar_cliente" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Salvar Cliente
        </button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
    <h2 class="text-2xl font-semibold mb-4">Clientes Cadastrados</h2>
    <table class="min-w-full bg-white">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-4 text-left">Nome</th>
                <th class="py-2 px-4 text-left">Email</th>
                <th class="py-2 px-4 text-left">Telefone</th>
                <th class="py-2 px-4 text-left">Endereço</th>
                <th class="py-2 px-4 text-left">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_clientes->num_rows > 0): ?>
                <?php while ($cliente = $result_clientes->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($cliente['nome']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($cliente['email']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($cliente['endereco']); ?></td>
                        <td class="py-2 px-4">
                            <a href="#" class="text-yellow-500 hover:text-yellow-700">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">Nenhum cliente cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php

$conn->close();
require '../templates/footer.php';
?>