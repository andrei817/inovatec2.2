<?php
// Inclua seu arquivo de configuração
include("php/Config.php");

// Verifique se o parâmetro 'delete' está presente na URL
if (isset($_GET['delete'])) {
    $id = $_GET['delete']; // Pega o ID do buffet a ser excluído

    // Passo 1: Remove as referências ao buffet na tabela intermediária 'evento_buffet'
    $deleteEventoBuffetSql = "DELETE FROM evento_buffet WHERE buffet_id = ?";
    if ($stmt = $conn->prepare($deleteEventoBuffetSql)) {
        $stmt->bind_param("i", $id); // 'i' para inteiro (id do buffet)
        $stmt->execute();
        $stmt->close();
    }

    

    // Passo 2: Atualiza os eventos para remover a referência ao buffet
    $updateEventosSql = "UPDATE eventos SET buffet_id = NULL WHERE buffet_id = ?";
    if ($stmt = $conn->prepare($updateEventosSql)) {
        $stmt->bind_param("i", $id); // 'i' para inteiro (id do buffet)
        $stmt->execute();
        $stmt->close();
    }

    // Passo 3: Agora, exclua o buffet da tabela buffet
    $deleteBuffetSql = "DELETE FROM buffet WHERE id = ?";
    if ($stmt = $conn->prepare($deleteBuffetSql)) {
        $stmt->bind_param("i", $id); // 'i' para inteiro (id do buffet)
        if ($stmt->execute()) {
            echo "Buffet excluído com sucesso!";
            header("Location: lista de buffet.php"); // Redireciona para a lista de buffets após a exclusão
            exit;
        } else {
            echo "Erro ao excluir o buffet.";
        }
        $stmt->close();
    }

} else {
    echo "ID não fornecido.";
    exit;
}

// Fechar a conexão com o banco
$conn->close();
?>
