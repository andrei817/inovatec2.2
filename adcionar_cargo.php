<?php
// Incluir o arquivo de configuração do banco de dados
include('php/Config.php');

$cadastroSucesso = false;
$cargoDuplicado = false;

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receber e sanitizar os dados do formulário
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);

    // Validar os campos
    if (empty($nome)) {
        echo "Por favor, preencha todos os campos.";
    } else {
        // Verificar se o cargo já existe
        $sqlCheck = "SELECT id FROM cargos WHERE nome = '$nome'";
        $checkResult = mysqli_query($conn, $sqlCheck);

        if (mysqli_num_rows($checkResult) > 0) {
            // Se o cargo já existir, define a variável para exibir o modal de erro
            $cargoDuplicado = true;
        } else {
            // Inserir o cargo no banco de dados
            $sql = "INSERT INTO cargos (nome) VALUES ('$nome')";
            if (mysqli_query($conn, $sql)) {
                // echo "Cargo adicionado com sucesso!";
            } else {
                echo "Erro ao adicionar o cargo: " . mysqli_error($conn);
            }
            $cadastroSucesso = true;
        }
    }
}

// Buscar os eventos disponíveis para exibição no formulário
$eventos_sql = "SELECT id, nome FROM eventos";
$eventos_result = mysqli_query($conn, $eventos_sql);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="ico/SGE.ico" type="image/x-icon">
    <link rel="stylesheet" href="adcionar cargo.css">
    <title>Adicionar Cargo</title>
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



<section class="agenda-evento">

<div class="conteudo">

<section class="login-section">

        <div class="login-box">

    <a href="cargo.php" class="btn-close-cargo">&times;</a>
    <h1>Adicionar Cargo de Staff</h1>

<form action="" method="POST">

    <div class="input-group">
        <label for="nome">Nome do Cargo:</label>
        <input type="text" name="nome" id="nome" required>
    </div>

        <button type="submit" class="login-btn-cargo">Adicionar</button>
        <a href="cargo.php"><button type="button" class="Cancel-btn-cargo">Cancelar</button></a>
    </form>

    <!-- Modal de Sucesso -->
<div id="modalSucesso" class="modal-correto">
    <div class="modal-content-correto">
         <span class="close-icon" onclick="fecharModal()">&times;</span>
        <h2>Cargo Adcionado com Sucesso!</h2>
        <img src="correto.png" class="correto-img">
       
    </div>
</div>


<!-- Modal de Cargo Duplicado -->
<div id="modalDuplicado" class="modal-incorreto" style="display: none;">
    <div class="modal-content-incorreto">
        <span class="close-icon-incorreto" onclick="fecharModalDuplicado('modalDuplicado')">&times;</span>
        <h1>Erro: Cargo Já Cadastrado!</h1>
        <p>O cargo com o nome informado já existe no sistema.</p>
        <img src="erro2.png" class="erro-img">
    </div>
</div>

<script>
    // Função para fechar o modal
    function fecharModal() {
        document.getElementById("modalSucesso").style.display = "none";
    }

    function fecharModalDuplicado() {
        document.getElementById("modalDuplicado").style.display = "none";
    }
    

     // Função para redirecionar para outra página
     function redirecionarParaPagina() {
        window.location.href = "Cargo.php";  // Substitua com o URL da página desejada
    }

    // Exibe o modal se o cadastro foi bem-sucedido
    <?php if ($cadastroSucesso): ?>
        document.getElementById("modalSucesso").style.display = "flex";
        setTimeout(function() {
            fecharModal();           // Fecha o modal
            redirecionarParaPagina();  // Redireciona para outra página após 3 segundos
        }, 3000); // Fecha automaticamente após 3 segundos

        <?php elseif ($cargoDuplicado): ?>
        // Exibir modal de cargo duplicado
        document.getElementById("modalDuplicado").style.display = "flex";
    <?php endif; ?>

</script>


</body>
</html>
