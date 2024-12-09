<?php
include('php/Config.php');

$cadastroSucesso = false;
$eventoJaCadastrado = false; // Variável para controle do modal

// Função para normalizar o nome do arquivo
function normalizarNomeArquivo($nomeArquivo) {
    $nomeSemAcentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nomeArquivo);
    $nomeNormalizado = preg_replace('/[^A-Za-z0-9._-]/', '_', $nomeSemAcentos);
    return $nomeNormalizado;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura e validação dos dados do formulário
    $nome = $_POST['nome'] ?? ''; 
    $tema_id = $_POST['tema_id'] ?? '';
    $objetivo_id = $_POST['objetivo_id'] ?? '';
    $buffet_id = $_POST['buffet_id'] ?? '';
    $faixa_etaria_id = $_POST['faixa_etaria_id'] ?? ''; // Corrigido
    $status_social_id = $_POST['status_social_id'] ?? ''; // Corrigido
    $status_do_evento_id = $_POST['status_do_evento_id'] ?? '';
    $escolaridades_id = $_POST['escolaridades_id'] ?? ''; // Corrigido
    $data = $_POST['data'] ?? '';
    $local = $_POST['local'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $lotacao = $_POST['lotacao'] ?? '';
    $duracao = $_POST['duracao'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $imagem = $_FILES['imagem'] ?? null;

    // Validação para garantir que os campos obrigatórios estejam preenchidos
    if ($nome && $data && $local && $tema_id && $objetivo_id && $buffet_id && $faixa_etaria_id && $status_social_id && $status_do_evento_id && $escolaridades_id) {
        // Verificar se o evento já existe
        $stmt_check = $conn->prepare("SELECT 1 FROM eventos WHERE nome = ?");
        $stmt_check->bind_param("s", $nome);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Evento já cadastrado, define uma variável para acionar o modal
            $eventoJaCadastrado = true;
        } else {
            $eventoJaCadastrado = false;

            // Processamento da imagem
            $nomeImagem = null;
            if ($imagem && $imagem['error'] == UPLOAD_ERR_OK) {
                $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
                $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
                $tamanhoMaximo = 2 * 1024 * 1024;

                if (!in_array($extensao, $extensoesPermitidas)) {
                    die("Formato de arquivo inválido.");
                }

                if ($imagem['size'] > $tamanhoMaximo) {
                    die("O tamanho da imagem excede o limite de 2MB.");
                }

                $uploadDir = "uploads/eventos/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $nomeImagem = uniqid("evento_", true) . "_" . normalizarNomeArquivo($imagem['name']);
                $caminhoImagem = $uploadDir . $nomeImagem;

                if (!move_uploaded_file($imagem['tmp_name'], $caminhoImagem)) {
                    die("Erro ao mover a imagem.");
                }
            }

            // Preparação da consulta para inserir o evento
            $stmt = $conn->prepare("INSERT INTO eventos (nome, tema_id, objetivo_id, buffet_id, data, local, hora, lotacao, duracao, descricao, imagem, faixa_etaria_id, status_social_id, status_do_evento_id, escolaridades_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt === false) {
                die('Erro ao preparar a consulta: ' . $conn->error); // Exibe erro detalhado
            }

            // Bind dos parâmetros
            $stmt->bind_param("sssssssssssssss", $nome, $tema_id, $objetivo_id, $buffet_id, $data, $local, $hora, $lotacao, $duracao, $descricao, $nomeImagem, $faixa_etaria_id, $status_social_id, $status_do_evento_id, $escolaridades_id);

            // Executa a consulta
            if ($stmt->execute()) {
                $evento_id = $conn->insert_id; // Captura o ID do evento recém-inserido

                // Insere a associação nas tabelas intermediárias
                $stmt_assoc = $conn->prepare("INSERT INTO evento_buffet (evento_id, buffet_id) VALUES (?, ?)");
                $stmt_assoc->bind_param("ii", $evento_id, $buffet_id);
                $stmt_assoc->execute();
                $stmt_assoc->close();

                $stmt_assoc = $conn->prepare("INSERT INTO evento_objetivo (evento_id, objetivo_id) VALUES (?, ?)");
                $stmt_assoc->bind_param("ii", $evento_id, $objetivo_id);
                $stmt_assoc->execute();
                $stmt_assoc->close();

                $stmt_assoc = $conn->prepare("INSERT INTO evento_tema (evento_id, tema_id) VALUES (?, ?)");
                $stmt_assoc->bind_param("ii", $evento_id, $tema_id);
                $stmt_assoc->execute();
                $stmt_assoc->close();

                $cadastroSucesso = true;
            } else {
                echo "Erro ao cadastrar evento: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmt_check->close();
    } else {
        echo "Por favor, preencha todos os campos obrigatórios.";
    }

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="ico/SGE.ico" type="image/x-icon">
    <link rel="stylesheet" href="evento.css">
    <title>SGE - Cadastrar Eventos</title>

</head>

<body>

<!-- Carregando menu -->
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
<div class="content">
<div class="agenda-evento">
    <div class="conteudo">
        <!-- Formulário de Cadastro de Evento -->
        <section class="login-section">
            <div class="login-box">
            <h2>Cadastrar Evento</h2>
            <a href="lista de eventos.php" class="close-btn-evento">&times;</a>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="nome">Nome do Evento:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>


<?php
include("php/Config.php");

// Função para buscar os temas
function obterTemas() {
    global $conn;
    $sql = "SELECT id, nome FROM tema";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $temas = [];
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
        return $temas;
    }
    return [];
}
$temas = obterTemas();
?>


<div class="input-group">
    <label for="tema_id">Selecione o Tema:</label>
    <select name="tema_id" id="tema_id" required>
        <option value="">Escolha um tema</option>
        <?php
        if (count($temas) > 0) {
            // Loop para gerar as opções de tema
            foreach ($temas as $tema) {
                echo "<option value='" . $tema['id'] . "'>" . $tema['nome'] . "</option>";
            }
        } else {
            // Caso não haja temas cadastrados
            echo "<option value=''>Nenhum tema encontrado</option>";
        }
        ?>
    </select>
</div>

   <?php
   include("php/Config.php");
// Função para buscar os objetivos
function obterObjetivos() {
    global $conn;
    $sql = "SELECT id, nome FROM objetivo";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $objetivos = [];
        while ($row = $result->fetch_assoc()) {
            $objetivos[] = $row;
        }
        return $objetivos;
    }
    return [];
}
// Chama a função para obter os objetivos
$objetivos = obterObjetivos();
?>

<div class="input-group">
    <label for="objetivo_id">Selecione o Objetivo:</label>
    <select name="objetivo_id" id="objetivo_id" required>
        <option value="">Escolha um objetivo</option>
        <?php
        if (count($objetivos) > 0) {
            // Loop para gerar as opções de objetivo
            foreach ($objetivos as $objetivo) {
                echo "<option value='" . $objetivo['id'] . "'>" . $objetivo['nome'] . "</option>";
            }
        } else {
            // Caso não haja objetivos cadastrados
            echo "<option value=''>Nenhum objetivo encontrado</option>";
        }
        ?>
    </select>
</div>


<!-- Adicionei os campos de relacionamento com buffets da mesma maneira -->
   <?php

   include("php/Config.php");

   // Função para buscar os buffets
function obterBuffets() {
    global $conn;
    $sql = "SELECT id, nome FROM buffet";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $buffets = [];
        while ($row = $result->fetch_assoc()) {
            $buffets[] = $row;
        }
        return $buffets;
    }
    return [];
}
$buffets = obterBuffets()
?>

<div class="input-group">
    <label for="buffet_id">Selecione o Buffet:</label>
    <select name="buffet_id" id="buffet_id" required>
        <option value="">Escolha um buffet</option>
        <?php
        if (count($buffets) > 0) {
            // Loop para gerar as opções de buffet
            foreach ($buffets as $buffet) {
                echo "<option value='" . $buffet['id'] . "'>" . $buffet['nome'] . "</option>";
            }
        } else {
            // Caso não haja buffets cadastrados
            echo "<option value=''>Nenhum buffet encontrado</option>";
        }
        ?>
    </select>
</div>


                  <!-- Faixa Etária -->
            <div class="input-group">
                <label for="faixa_etaria_id">Faixa Etária:</label>
                <select id="faixa_etaria_id" name="faixa_etaria_id" >
                    <option value="">Escolha uma faixa etária</option>
                    <?php
                    include("php/Config.php");
                    $sql = "SELECT id, descricao FROM faixa_etaria";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['descricao'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhuma faixa etária encontrada</option>";
                    }
                    $conn->close();
                    ?>
                </select>
            </div>


                            <!-- Status Social -->
                <div class="input-group">
                    <label for="status_social_id">Status Social:</label>
                    <select id="status_social_id" name="status_social_id" required>
                        <option value="">Escolha um status social</option>
                        <?php
                        include("php/Config.php");
                        $sql = "SELECT id, descricao FROM status_social";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['descricao'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>Nenhum status social encontrado</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>

                <!-- Status do Evento -->
                <div class="input-group">
                    <label for="status_do_evento_id">Status do Evento:</label>
                    <select name="status_do_evento_id" id="status_do_evento_id" required>
                        <option value="">Escolha um status</option>
                        <?php
                        include("php/Config.php");
                        $sql = "SELECT id, nome FROM status_do_evento";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>Nenhum status encontrado</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>

               <!-- Escolaridade -->
            <div class="input-group">
                <label for="escolaridades_id">Escolaridade:</label>
                <select id="escolaridades_id" name="escolaridades_id" required>
                    <option value="">Escolha uma escolaridade</option>
                    <?php
                    include("php/Config.php");
                    $sql = "SELECT id, descricao FROM escolaridades";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['descricao'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhuma escolaridade encontrada</option>";
                    }
                    $conn->close();
                    ?>
                </select>
            </div>


                <div class="input-group">
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" required>
                </div>

                <div class="input-group">
                    <label for="local">Local:</label>
                    <input type="text" id="local" name="local" required>
                </div>

                <div class="input-group">
                    <label for="hora">Hora:</label>
                    <input type="time" id="hora" name="hora" required>
                </div>

                <div class="input-group">
                    <label for="lotacao">Lotação:</label>
                    <input type="number" id="lotacao" name="lotacao" required>
                </div>


                <div class="input-group">
                    <label for="duracao">Duração:</label>
                    <input type="time" id="duracao" name="duracao" required>
                </div>

                <div class="input-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" rows="3" cols="70" class="inputUser" placeholder="Descrição do Evento" required></textarea>
                </div>
                    

                
 <button type="submit" name="cadastrar" class="login-btn-evento">Cadastrar</button>
    <a href="lista de eventos.php"><button type="button" class="Cancel-btn-evento">Cancelar</button></a>
    
                
   
    <div class="image-container">
    <div class="form-row image-row">
        <label for="imagem" class="custom-file-button">Escolher Imagem</label>
        <input type="file" id="imagem" name="imagem" accept="image/*" required onchange="previewImage()">

        <!-- Pré-visualização da imagem ou mensagem padrão -->
        <div class="image-preview">
            <img id="image-preview" src="image-not-found.png" alt="Nenhuma Imagem Escolhida" style="max-width: 200px; max-height: 200px;">
            <span id="no-image-message" style="display: none; font-size: 16px; color: #888;">Nenhuma imagem escolhida</span>
        </div>
    </div>
</div>


<script>
    // Função para atualizar a pré-visualização da imagem
    function previewImage() {
        var fileInput = document.getElementById("imagem");
        var file = fileInput.files[0];
        var preview = document.getElementById("image-preview");
        var noImageMessage = document.getElementById("no-image-message");

        if (file) {
            // Exibe a imagem escolhida
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = "block";
                noImageMessage.style.display = "none";  // Esconde a mensagem
            };
            reader.readAsDataURL(file);
        } else {
            // Se não houver imagem, exibe a mensagem padrão
            preview.src = "image-not-found.png";
            preview.style.display = "none";  // Esconde a imagem
            noImageMessage.style.display = "block";  // Exibe a mensagem
        }
    }
</script>

</div>

    
    </div>

    

   
</form>

<script>
   function previewImage() {
    var fileInput = document.getElementById('imagem');
    var imagePreview = document.getElementById('image-preview');

    // Verificar se o usuário escolheu uma imagem
    if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            // Se o usuário escolher uma imagem, exibe a imagem
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';  // Exibe a imagem
        };
        
        reader.readAsDataURL(fileInput.files[0]);
    } else {
        // Se não escolher uma imagem, exibe uma imagem padrão
        imagePreview.src = "image-not-found.png";  // Caminho para a imagem padrão
        imagePreview.style.display = 'block';  // Exibe a imagem
    }
}

</script>


            </form>

<!-- Modal de Sucesso ou Erro -->
<div id="modalResultado" class="modal-resultado">
    <div class="modal-content-resultado">
        <span class="close-icon" onclick="fecharModal()">&times;</span>
        
        <!-- Conteúdo de Sucesso -->
        <div id="conteudoSucesso" style="display:none;">
            <h2>Evento Cadastrado com Sucesso!</h2>
            <img src="correto.png" class="correto-img">
        </div>
        
        <!-- Conteúdo de Erro -->
        <div id="conteudoErro" style="display:none;">
            <h1>Erro</h1>
            <p>Este evento já está cadastrado.</p>
            <img src="erro2.png" class="erro-img">
        </div>
    </div>
</div>

<script>
// Função para fechar o modal
function fecharModal() {
    document.getElementById("modalResultado").style.display = "none";
}

// Função para redirecionar para outra página
function redirecionarParaPagina() {
    window.location.href = "lista de eventos.php";  // Substitua com o URL da página desejada
}

// Exibe o modal e ajusta o conteúdo de acordo com o sucesso ou erro
<?php if ($cadastroSucesso): ?>
    window.onload = function() {
        document.getElementById('conteudoSucesso').style.display = 'block'; // Exibe sucesso
        document.getElementById('modalResultado').style.display = 'flex';
        setTimeout(function() {
            fecharModal(); // Fecha o modal
            redirecionarParaPagina(); // Redireciona após 3 segundos
        }, 3000); // Fecha automaticamente após 3 segundos
    };
<?php elseif ($eventoJaCadastrado): ?>
    window.onload = function() {
        document.getElementById('conteudoErro').style.display = 'block'; // Exibe erro
        document.getElementById('modalResultado').style.display = 'flex';
    };
<?php endif; ?>
</script>


</div>


</body>
</html>


