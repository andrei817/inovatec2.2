<?php
include("php/Config.php");

// Verificar se o evento está sendo editado
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];

    // Buscar dados do evento para editar
    $sql = "SELECT * FROM eventos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

// Inserir novas associações para buffets
if (!empty($_POST['buffet_ids'])) {
    foreach ($_POST['buffet_ids'] as $buffet_id) {
        $stmtBuffet = $conn->prepare("INSERT INTO evento_buffet (evento_id, buffet_id) VALUES (?, ?)");
        $stmtBuffet->bind_param("ii", $id, $buffet_id);
        $stmtBuffet->execute();
    }
}

// Inserir novas associações para objetivos
if (!empty($_POST['objetivo_ids'])) {
    foreach ($_POST['objetivo_ids'] as $objetivo_id) {
        $stmtObjetivo = $conn->prepare("INSERT INTO evento_objetivo (evento_id, objetivo_id) VALUES (?, ?)");
        $stmtObjetivo->bind_param("ii", $id, $objetivo_id);
        $stmtObjetivo->execute();
    }
}

// Inserir novas associações para temas
if (!empty($_POST['tema_ids'])) {
    foreach ($_POST['tema_ids'] as $tema_id) {
        $stmtTema = $conn->prepare("INSERT INTO evento_tema (evento_id, tema_id) VALUES (?, ?)");
        $stmtTema->bind_param("ii", $id, $tema_id);
        $stmtTema->execute();
    }
}

    

// Verificar se o formulário de edição foi enviado
if (isset($_POST['atualizar'])) {
    // Coletar os dados do formulário
    $nome = $_POST['nome'];
    $objetivo_id = $_POST['objetivo_id'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $duracao = $_POST['duracao'];
    $tema_id = $_POST['tema_id'];
    $buffet_id = $_POST['buffet_id'];
    $local = $_POST['local'];
    $lotacao = $_POST['lotacao'];
    $descricao = $_POST['descricao'];
    $faixa_etaria_id = $_POST['faixa_etaria_id'];
    $status_social_id = $_POST['status_social_id'];
    $status_do_evento_id = $_POST['status_do_evento_id'];
    $escolaridades_id = $_POST['escolaridades_id'];
    $id = $_POST['id'];  // Supondo que você também está recebendo o ID do evento para atualizar

    // Verificar se uma nova imagem foi carregada
    if (!empty($_FILES['imagem']['name'])) {
        $imagem_nome = basename($_FILES['imagem']['name']);
        $target_dir = "uploads/eventos/";  // Diretório para armazenar as imagens
        $target_file = $target_dir . $imagem_nome;  // Caminho final do arquivo

        // Validar se o arquivo foi movido para o diretório de destino
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
            // Atualizar o evento incluindo a nova imagem
            $sqlUpdate = "UPDATE eventos SET 
                          nome = ?, 
                          objetivo_id = ?, 
                          data = ?, 
                          hora = ?,  
                          duracao = ?, 
                          tema_id = ?, 
                          buffet_id = ?, 
                          local = ?, 
                          lotacao = ?, 
                          descricao = ?, 
                          faixa_etaria_id = ?, 
                          status_social_id = ?, 
                          status_do_evento_id = ?, 
                          escolaridades_id = ?, 
                          imagem = ? 
                          WHERE id = ?";

            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sssssssssssssssi", 
                $nome, $objetivo_id, $data, $hora, $duracao, 
                $tema_id, $buffet_id, $local, $lotacao, $descricao,
                $faixa_etaria_id, $status_social_id, $status_do_evento_id, $escolaridades_id,
                $imagem_nome, $id);  // Aqui são 16 parâmetros, com a imagem
        } else {
            echo "Erro ao fazer upload da imagem.";
            exit();
        }
    } else {
        // Se nenhuma nova imagem for carregada, não atualiza o campo de imagem
        $sqlUpdate = "UPDATE eventos SET 
                      nome = ?, 
                      objetivo_id = ?, 
                      data = ?, 
                      hora = ?,  
                      duracao = ?, 
                      tema_id = ?, 
                      buffet_id = ?, 
                      local = ?, 
                      lotacao = ?, 
                      descricao = ?, 
                      faixa_etaria_id = ?, 
                      status_social_id = ?, 
                      status_do_evento_id = ?, 
                      escolaridades_id = ? 
                      WHERE id = ?";

        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssssssssssssssi", 
            $nome, $objetivo_id, $data, $hora, $duracao, 
            $tema_id, $buffet_id, $local, $lotacao, $descricao,
            $faixa_etaria_id, $status_social_id, $status_do_evento_id, $escolaridades_id, $id);
    }

    // Executar a atualização
    if ($stmtUpdate->execute()) {
        // Deletar entradas antigas das tabelas intermediárias após a atualização do evento
        $conn->query("DELETE FROM evento_buffet WHERE evento_id = $id");
        $conn->query("DELETE FROM evento_objetivo WHERE evento_id = $id");
        $conn->query("DELETE FROM evento_tema WHERE evento_id = $id");

        // Inserir novas associações para buffets
        if (!empty($_POST['buffet_ids'])) {
            foreach ($_POST['buffet_ids'] as $buffet_id) {
                $stmtBuffet = $conn->prepare("INSERT INTO evento_buffet (evento_id, buffet_id) VALUES (?, ?)");
                $stmtBuffet->bind_param("ii", $id, $buffet_id);
                $stmtBuffet->execute();
            }
        }

        // Inserir novas associações para objetivos
        if (!empty($_POST['objetivo_ids'])) {
            foreach ($_POST['objetivo_ids'] as $objetivo_id) {
                $stmtObjetivo = $conn->prepare("INSERT INTO evento_objetivo (evento_id, objetivo_id) VALUES (?, ?)");
                $stmtObjetivo->bind_param("ii", $id, $objetivo_id);
                $stmtObjetivo->execute();
            }
        }

        // Inserir novas associações para temas
        if (!empty($_POST['tema_ids'])) {
            foreach ($_POST['tema_ids'] as $tema_id) {
                $stmtTema = $conn->prepare("INSERT INTO evento_tema (evento_id, tema_id) VALUES (?, ?)");
                $stmtTema->bind_param("ii", $id, $tema_id);
                $stmtTema->execute();
            }
        }

        echo "Evento atualizado com sucesso!";
        header("Location: lista de eventos.php");
        exit();
    } else {
        echo "Erro ao atualizar evento: " . $conn->error;
    }
}
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="ico/SGE.ico" type="image/x-icon">
    <link rel="stylesheet" href="edit evento.css">
    <title>SGE- Editar Evento</title>
</head>
<body>
    
<div id="header"></div> <!-- Div onde o menu será injetado -->

<script>
  fetch('/menu principal.php')
    .then(response => response.text())
    .then(data => {
      document.getElementById('header').innerHTML = data;
    })
    .catch(error => console.error('Erro ao carregar o menu:', error));
</script>

<div class="content">
  <!-- Conteúdo da página -->
</div>

<script> 
   function abrirSidebar() {
    if (window.innerWidth <= 768) {
      document.getElementById("mySidebar").style.width = "100%";
    } else {
      document.getElementById("mySidebar").style.width = "310px";
    }
    // Adiciona a classe "aberto" à sidebar
    document.getElementById("mySidebar").classList.add("aberto");
  }

  // Função para fechar a sidebar
  function fecharSidebar() {
    document.getElementById("mySidebar").style.width = "0";
    // Remove a classe "aberto"
    document.getElementById("mySidebar").classList.remove("aberto");
  }

  // Adiciona o evento para fechar ao clicar fora da sidebar
  document.addEventListener('click', function (event) {
    const sidebar = document.getElementById("mySidebar");
    const isClickInsideSidebar = sidebar.contains(event.target);
    const isClickOnButton = event.target.closest('.open-btn');

    // Fecha a sidebar se o clique não for nela nem no botão de abrir
    if (!isClickInsideSidebar && !isClickOnButton && sidebar.classList.contains("aberto")) {
      fecharSidebar();
    }
  });

  // Fecha a sidebar ao clicar nos links
  document.querySelectorAll('#mySidebar a').forEach(link => {
    link.addEventListener('click', fecharSidebar);
  });
   </script>


<script>
  // Função para mostrar/ocultar a lista suspensa do perfil
  function toggle() {
      var profileDropdownList = document.querySelector('.profile-dropdown-list');
      profileDropdownList.classList.toggle('active');
  }

  // Função para mostrar o modal de logout
  function showLogoutModal() {
      document.getElementById('logoutModal').style.display = 'flex';
  }

  // Função para fechar qualquer modal
  function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
  }

  // Função para confirmar o logout e mostrar o modal de agradecimento
  function confirmLogout() {
      closeModal('logoutModal'); // Fecha o modal de logout
      document.getElementById('thankYouModal').style.display = 'flex'; // Mostra o modal de agradecimento
      
      // Redireciona após alguns segundos (opcional)
      setTimeout(function() {
          window.location.href = 'index.php'; // Redireciona para a página inicial
      }, 2000); // Aguarda 2 segundos antes de redirecionar
  }
</script>

<!-- Conteúdo principal -->
<div class="agenda-evento">
    <div class="content">
        <section class="agenda">
            <!-- Formulário de Cadastro de Evento -->
            <div class="form-container">
                <h2>Editar Evento</h2>
                <a href="lista de eventos.php" class="close-btn-evento">&times;</a>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                    <div class="input-group">
                        <label for="nome">Nome do Evento:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo $row['nome']; ?>" required>
                    </div>

                    <div class="input-group">
    <label for="objetivo_ids">Objetivos:</label>
    <select name="objetivo_ids[]" id="objetivo_ids">
        <?php
        // Consulta para buscar todos os objetivos
        $objetivo_sql = "SELECT * FROM objetivo";
        $objetivo_result = $conn->query($objetivo_sql);
        
        // Buscar os objetivos já associados ao evento
        $objetivo_selecionados = [];
        $objetivo_evento_sql = "SELECT objetivo_id FROM evento_objetivo WHERE evento_id = ?";
        $stmt_objetivo = $conn->prepare($objetivo_evento_sql);
        $stmt_objetivo->bind_param("i", $id);
        $stmt_objetivo->execute();
        $result_objetivo_evento = $stmt_objetivo->get_result();
        
        while ($objetivo_evento = $result_objetivo_evento->fetch_assoc()) {
            $objetivo_selecionados[] = $objetivo_evento['objetivo_id'];
        }

        // Gerar opções com base nos objetivos disponíveis
        while ($objetivo = $objetivo_result->fetch_assoc()) {
            echo "<option value='" . $objetivo['id'] . "' " . (in_array($objetivo['id'], $objetivo_selecionados) ? 'selected' : '') . ">" . $objetivo['nome'] . "</option>";
        }
        ?>
    </select>
</div>


                    <div class="input-group">
                        <label for="data">Data:</label>
                        <input type="date" id="data" name="data" value="<?php echo $row['data']; ?>" required>
                    </div>

                    <div class="input-group">
                        <label for="hora">Hora:</label>
                        <input type="time" id="hora" name="hora" value="<?php echo $row['hora']; ?>">
                    </div>

                    <div class="input-group">
                        <label for="duracao">Duração:</label>
                        <input type="time" id="duracao" name="duracao" value="<?php echo $row['duracao']; ?>">
                    </div>

                    <div class="input-group">
    <label for="tema_ids">Temas:</label>
    <select name="tema_ids[]" id="tema_ids">
        <?php
        // Consulta para buscar todos os temas
        $tema_sql = "SELECT * FROM tema";
        $tema_result = $conn->query($tema_sql);
        
        // Buscar os temas já associados ao evento
        $tema_selecionados = [];
        $tema_evento_sql = "SELECT tema_id FROM evento_tema WHERE evento_id = ?";
        $stmt_tema = $conn->prepare($tema_evento_sql);
        $stmt_tema->bind_param("i", $id);
        $stmt_tema->execute();
        $result_tema_evento = $stmt_tema->get_result();
        
        while ($tema_evento = $result_tema_evento->fetch_assoc()) {
            $tema_selecionados[] = $tema_evento['tema_id'];
        }

        // Gerar opções com base nos temas disponíveis
        while ($tema = $tema_result->fetch_assoc()) {
            echo "<option value='" . $tema['id'] . "' " . (in_array($tema['id'], $tema_selecionados) ? 'selected' : '') . ">" . $tema['nome'] . "</option>";
        }
        ?>
    </select>
</div>


<div class="input-group">
    <label for="buffet_ids">Buffets:</label>
    <select name="buffet_ids[]" id="buffet_ids" >
        <?php
        // Consulta para buscar todos os buffets
        $buffet_sql = "SELECT * FROM buffet";
        $buffet_result = $conn->query($buffet_sql);
        
        // Buscar os buffets já associados ao evento
        $buffet_selecionados = [];
        $buffet_evento_sql = "SELECT buffet_id FROM evento_buffet WHERE evento_id = ?";
        $stmt_buffet = $conn->prepare($buffet_evento_sql);
        $stmt_buffet->bind_param("i", $id);
        $stmt_buffet->execute();
        $result_buffet_evento = $stmt_buffet->get_result();
        
        while ($buffet_evento = $result_buffet_evento->fetch_assoc()) {
            $buffet_selecionados[] = $buffet_evento['buffet_id'];
        }

        // Gerar opções com base nos buffets disponíveis
        while ($buffet = $buffet_result->fetch_assoc()) {
            echo "<option value='" . $buffet['id'] . "' " . (in_array($buffet['id'], $buffet_selecionados) ? 'selected' : '') . ">" . $buffet['nome'] . "</option>";
        }
        ?>
    </select>
</div>




                    <div class="input-group">
    <label for="faixa_etaria_id">Faixa Etária:</label>
    <select name="faixa_etaria_id" id="faixa_etaria_id">
        <?php
        $faixa_sql = "SELECT * FROM faixa_etaria";
        $faixa_result = $conn->query($faixa_sql);
        while ($faixa = $faixa_result->fetch_assoc()) {
            echo "<option value='" . $faixa['id'] . "' " . ($faixa['id'] == $row['faixa_etaria_id'] ? 'selected' : '') . ">" . $faixa['descricao'] . "</option>";
        }
        ?>
    </select>
</div>

<div class="input-group">
    <label for="status_social_id">Status Social:</label>
    <select name="status_social_id" id="status_social_id">
        <?php
        $status_sql = "SELECT * FROM status_social";
        $status_result = $conn->query($status_sql);
        while ($status = $status_result->fetch_assoc()) {
            echo "<option value='" . $status['id'] . "' " . ($status['id'] == $row['status_social_id'] ? 'selected' : '') . ">" . $status['descricao'] . "</option>";
        }
        ?>
    </select>
</div>

<div class="input-group">
    <label for="status_do_evento_id">Status do Evento:</label>
    <select name="status_do_evento_id" id="status_do_evento_id">
        <?php
        $evento_sql = "SELECT * FROM status_do_evento";
        $evento_result = $conn->query($evento_sql);
        while ($evento = $evento_result->fetch_assoc()) {
            echo "<option value='" . $evento['id'] . "' " . ($evento['id'] == $row['status_do_evento_id'] ? 'selected' : '') . ">" . $evento['nome'] . "</option>";
        }
        ?>
    </select>
</div>

<div class="input-group">
    <label for="escolaridades_id">Escolaridade:</label>
    <select name="escolaridades_id" id="escolaridades_id">
        <?php
        $escolaridade_sql = "SELECT * FROM escolaridades";
        $escolaridade_result = $conn->query($escolaridade_sql);
        while ($escolaridades = $escolaridade_result->fetch_assoc()) {
            echo "<option value='" . $escolaridades['id'] . "' " . ($escolaridades['id'] == $row['escolaridades_id'] ? 'selected' : '') . ">" . $escolaridades['descricao'] . "</option>";
        }
        ?>
    </select>
</div>


                    <div class="input-group">
                        <label for="local">Local:</label>
                        <input type="text" id="local" name="local" value="<?php echo $row['local']; ?>">
                    </div>

                    <div class="input-group">
                        <label for="lotacao">Lotação:</label>
                        <input type="number" id="lotacao" name="lotacao" value="<?php echo $row['lotacao']; ?>">
                    </div>

                    <div class="input-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="2" class="inputUser"><?php echo $row['descricao']; ?></textarea>
                    </div>

    <button type="submit" name="atualizar" class=login-btn-evento>Atualizar</button>
     <a href="lista de eventos.php"><button type="button" class="Cancel-btn-evento">Cancelar</button></a>   

     <div class="image-container">
    <div class="form-row image-row">
        <!-- Rótulo para o campo de seleção de imagem -->
        <label for="imagem">Imagem do Evento:</label>

        <!-- Campo de arquivo oculto com estilo personalizado -->
        <input type="file" id="imagem" name="imagem" accept="image/*" onchange="updateFileName()">

        <!-- Campo de texto para mostrar o nome do arquivo -->
        <input type="text" id="file-name" 
               value="<?php echo !empty($row['imagem']) ? htmlspecialchars($row['imagem']) : ''; ?>" 
               readonly 
               style="background-color: #f0f0f0; cursor: default;">

        <!-- Exibição da imagem atual -->
        <?php if (!empty($row['imagem'])): ?>
            <div class="current-image">
                <img src="uploads/eventos/<?php echo htmlspecialchars($row['imagem']); ?>" 
                     alt="Imagem do Evento Atual" 
                     style="max-width: 200px; max-height: 200px;">
            </div>
        <?php endif; ?>

        <!-- Pré-visualização da nova imagem selecionada -->
        <div class="image-preview">
            <img id="image-preview" src="#" alt="Pré-visualização da Imagem" 
                 style="display: none; max-width: 200px; max-height: 200px;">
        </div>

        <!-- Botão personalizado -->
        <label for="imagem" class="custom-file-button">Escolher Imagem</label>
    </div>
</div>

<script>
    // Função para atualizar o nome do arquivo e gerenciar a pré-visualização
    function updateFileName() {
        const fileInput = document.getElementById("imagem");
        const fileNameField = document.getElementById("file-name");
        const currentImage = document.querySelector(".current-image"); // Seleciona a imagem atual
        const imagePreview = document.getElementById("image-preview");

        // Atualiza o nome do arquivo no campo de texto
        if (fileInput.files.length > 0) {
            fileNameField.value = fileInput.files[0].name;
        } else {
            fileNameField.value = "";
        }

        // Exibe a pré-visualização da imagem selecionada
        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = "block";

            // Esconde a imagem atual, se houver
            if (currentImage) {
                currentImage.style.display = "none";
            }
        };

        // Lê o arquivo selecionado e exibe a pré-visualização
        if (fileInput.files[0]) {
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            // Se o campo de arquivo for limpo, restaura a imagem atual
            imagePreview.style.display = "none";
            if (currentImage) {
                currentImage.style.display = "block";
            }
        }
    }
</script>

                </form>
            </div>
        </section>
    </div>
</div>

</body>
</html>
