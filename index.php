<!DOCTYPE html>
<html>
<head>
    <title>Formulário PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #333;
            color: #fff;
        }

        .delete-btn {
            background-color: #cc0000;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtém os valores do formulário
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $telefone = $_POST["telefone"];

        // Validação simples (você pode adicionar mais validações aqui)
        if (empty($nome) || empty($email) || empty($telefone)) {
            echo "<p>Preencha todos os campos do formulário.</p>";
        } else {
            // Salva os valores em um array na variável de sessão
            $dados = array("nome" => $nome, "email" => $email, "telefone" => $telefone);

            // Verifica se a variável de sessão já existe
            if (!isset($_SESSION["valores"])) {
                $_SESSION["valores"] = array();
            }

            // Verifica se está editando um valor existente
            if (isset($_POST["indice"]) && !empty($_POST["indice"])) {
                $indice = $_POST["indice"];
                if (isset($_SESSION["valores"][$indice])) {
                    // Atualiza os valores
                    $_SESSION["valores"][$indice] = $dados;
                    echo "<p>As informações foram atualizadas com sucesso.</p>";
                }
            } else {
                // Adiciona os valores ao array na variável de sessão
                $_SESSION["valores"][] = $dados;
                echo "<p>As informações foram salvas com sucesso.</p>";
            }
        }
    }

    // Excluir valor
    if (isset($_GET["excluir"]) && !empty($_GET["excluir"])) {
        $indice = $_GET["excluir"];
        if (isset($_SESSION["valores"][$indice])) {
            unset($_SESSION["valores"][$indice]);
            $_SESSION["valores"] = array_values($_SESSION["valores"]);
        }
    }
    ?>

    <h2>Formulário de Informações</h2>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="hidden" name="indice" id="edit-indice" value="">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome"><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email"><br><br>

        <label for="telefone">Telefone:</label>
        <input type="text" name="telefone" id="telefone"><br><br>

        <input type="submit" value="Enviar">
    </form>

    <h2>Valores Salvos</h2>
    <?php
    // Exibe os valores da variável de sessão
    if (isset($_SESSION["valores"]) && !empty($_SESSION["valores"])) {
        echo "<table>";
        echo "<tr><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr>";
        foreach ($_SESSION["valores"] as $indice => $dados) {
            echo "<tr>";
            echo "<td>{$dados['nome']}</td>";
            echo "<td>{$dados['email']}</td>";
            echo "<td>{$dados['telefone']}</td>";
            echo "<td class='actions'>
                    <button class='edit-btn' onclick='editarValor({$indice})'>Editar</button>
                    <button class='delete-btn' onclick='excluirValor({$indice})'>Excluir</button>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhum valor salvo ainda.</p>";
    }
    ?>

    <h2>Editar Valor</h2>
    <div id="editar-form" style="display: none;">
        <form id="editar-form-inner" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="hidden" id="edit-indice-hidden" name="indice" value="">
            <label for="edit-nome">Nome:</label>
            <input type="text" id="edit-nome" name="nome"><br><br>

            <label for="edit-email">Email:</label>
            <input type="email" id="edit-email" name="email"><br><br>

            <label for="edit-telefone">Telefone:</label>
            <input type="text" id="edit-telefone" name="telefone"><br><br>

            <input type="submit" value="Salvar">
            <button type="button" onclick="cancelarEdicao()">Cancelar</button>
        </form>
    </div>

    <script>
        function excluirValor(indice) {
            if (confirm("Tem certeza que deseja excluir este valor?")) {
                window.location.href = "?excluir=" + indice;
            }
        }

        function editarValor(indice) {
            var valor = <?php echo json_encode(isset($_SESSION["valores"]) && !empty($_SESSION["valores"]) ? $_SESSION["valores"] : []); ?>;
            if (valor[indice]) {
                document.getElementById("edit-indice-hidden").value = indice;
                document.getElementById("edit-nome").value = valor[indice].nome;
                document.getElementById("edit-email").value = valor[indice].email;
                document.getElementById("edit-telefone").value = valor[indice].telefone;
                document.getElementById("editar-form").style.display = "block";
                window.scrollTo(0,document.body.scrollHeight);
            }
        }

        function cancelarEdicao() {
            document.getElementById("edit-indice-hidden").value = "";
            document.getElementById("edit-nome").value = "";
            document.getElementById("edit-email").value = "";
            document.getElementById("edit-telefone").value = "";
            document.getElementById("editar-form").style.display = "none";
        }
    </script>
</body>
</html>
