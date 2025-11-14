<?php
require '../templates/header.php';
require '../auth_check.php';
require '../database/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar_usuario'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);
    $nivel = $conn->real_escape_string($_POST['nivel_acesso']);

    // IMPORTANTE: Gerar HASH da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ssss", $nome, $email, $senha_hash, $nivel);

    if ($stmt->execute()) {
        $mensagem = "Usuário cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar: " . $stmt->error;
    }
    $stmt->close();
}

$sql_select = "SELECT id_usuario, nome, email, nivel_acesso FROM usuarios ORDER BY nome";
$result_usuarios = $conn->query($sql_select);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Gestão de Usuários</h1>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4">Cadastrar Novo Usuário</h2>

    <?php if (isset($mensagem)): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <form action="usuarios.php" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-gray-700">Nome</label>
                <input type="text" name="nome" required class="w-full px-3 py-2 border rounded">
            </div>
            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded">
            </div>
            <div>
                <label class="block text-gray-700">Senha</label>
                <input type="password" name="senha" required class="w-full px-3 py-2 border rounded">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Nível de Acesso</label>
            <select name="nivel_acesso" class="w-full px-3 py-2 border rounded">
                <option value="vendedor">Vendedor</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <button type="submit" name="cadastrar_usuario"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Salvar Usuário
        </button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Usuários Cadastrados</h2>
    <table class="min-w-full bg-white">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-4 text-left">Nome</th>
                <th class="py-2 px-4 text-left">Email</th>
                <th class="py-2 px-4 text-left">Nível</th>
                <th class="py-2 px-4 text-left">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result_usuarios->fetch_assoc()): ?>
                <tr class="border-b">
                    <td class="py-2 px-4"><?php echo htmlspecialchars($user['nome']); ?></td>
                    <td class="py-2 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="py-2 px-4"><?php echo htmlspecialchars($user['nivel_acesso']); ?></td>
                    <td class="py-2 px-8">
                        <a href="#" class="text-yellow-500 hover:text-yellow-700 mx-2">Editar</a>
                        <a href="#" class="text-red-500 hover:text-red-700 mx-2">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
require '../templates/footer.php';
?>