<?php
// Incluir o arquivo de conexão
include("php/Config.php");

// Verifica se o ID do objetivo foi passado via GET e é válido
if (!isset($_GET['excluir']) || !is_numeric($_GET['excluir'])) {
    echo "<script>alert('ID do objetivo não fornecido ou inválido.'); window.location.href='objetivo lista.php';</script>";
    exit();
}

$id_objetivo = intval($_GET['excluir']); // Converte o ID para inteiro de forma segura

// Atualizar os eventos para remover a referência ao objetivo
$updateEventosSql = "UPDATE eventos SET objetivo_id = NULL WHERE objetivo_id = ?";
$stmt = $conn->prepare($updateEventosSql);
if ($stmt === false) {
    echo "<script>alert('Erro ao preparar a consulta para atualizar eventos.'); window.location.href='objetivo lista.php';</script>";
    exit();
}
$stmt->bind_param("i", $id_objetivo); // 'i' para inteiro (id do objetivo)
$stmt->execute();
$stmt->close(); // Fecha o statement após a execução da atualização

// Remover as referências na tabela evento_objetivo
$deleteEventoObjetivoSql = "DELETE FROM evento_objetivo WHERE objetivo_id = ?";
$stmt = $conn->prepare($deleteEventoObjetivoSql);
if ($stmt === false) {
    echo "<script>alert('Erro ao preparar a consulta para excluir referências do objetivo.'); window.location.href='objetivo lista.php';</script>";
    exit();
}
$stmt->bind_param("i", $id_objetivo); // 'i' para inteiro (id do objetivo)
$stmt->execute();
$stmt->close(); // Fecha o statement após a execução da exclusão das referências

// Agora, excluir o objetivo
$deleteObjetivoSql = "DELETE FROM objetivo WHERE id = ?";
$stmt = $conn->prepare($deleteObjetivoSql);
if ($stmt === false) {
    echo "<script>alert('Erro ao preparar a consulta para excluir o objetivo.'); window.location.href='objetivo lista.php';</script>";
    exit();
}
$stmt->bind_param("i", $id_objetivo); // 'i' para inteiro (id do objetivo)
if ($stmt->execute()) {
    header("Location: lista de objetivos.php");
    exit();
} else {
    echo "<script>alert('Erro ao excluir objetivo.'); window.location.href='objetivo lista.php';</script>";
}

$stmt->close(); // Fecha o statement após a execução da exclusão
?>
