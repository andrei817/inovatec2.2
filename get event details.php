<?php

if (isset($_GET['id'])) {
    $eventoId = intval($_GET['id']); // Certifique-se de sanitizar o ID recebido

    // Conectar ao banco de dados
    $conexao = mysqli_connect('localhost', 'root', '', 'bd_inovatec');
    if (!$conexao) {
        die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
    }

    // Consulta para buscar os detalhes do evento
    $sql = "
        SELECT 
            e.nome, e.data, e.hora, e.local, e.lotacao, e.duracao, e.descricao,
            t.nome AS tema_nome,
            o.nome AS objetivo_nome,
            b.nome AS buffet_nome
        FROM 
            eventos e
        LEFT JOIN 
            temas t ON e.tema_id = t.id
        LEFT JOIN 
            objetivos o ON e.objetivo_id = o.id
        LEFT JOIN 
            buffets b ON e.buffet_id = b.id
        WHERE 
            e.id = $eventoId
    ";

    $resultado = mysqli_query($conexao, $sql);
    $evento = mysqli_fetch_assoc($resultado);

    mysqli_close($conexao);

    if ($evento) {
        // Retorne os detalhes como HTML
        echo "<p><strong>Nome:</strong> " . htmlspecialchars($evento['nome']) . "</p>";
        echo "<p><strong>Data:</strong> " . htmlspecialchars(date('d/m/Y', strtotime($evento['data']))) . "</p>";
        echo "<p><strong>Horário:</strong> " . htmlspecialchars($evento['hora']) . "</p>";
        echo "<p><strong>Local:</strong> " . htmlspecialchars($evento['local']) . "</p>";
        echo "<p><strong>Lotação:</strong> " . htmlspecialchars($evento['lotacao']) . "</p>";
        echo "<p><strong>Duração:</strong> " . htmlspecialchars($evento['duracao']) . "</p>";
        echo "<p><strong>Descrição:</strong> " . nl2br(htmlspecialchars($evento['descricao'])) . "</p>";
        echo "<p><strong>Tema:</strong> " . htmlspecialchars($evento['tema_nome']) . "</p>";
        echo "<p><strong>Objetivo:</strong> " . htmlspecialchars($evento['objetivo_nome']) . "</p>";
        echo "<p><strong>Buffet:</strong> " . htmlspecialchars($evento['buffet_nome']) . "</p>";
    } else {
        echo "<p>Evento não encontrado.</p>";
    }
}
?>
