<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['email']) || !isset($_SESSION['id'])) {
    // Se o usuário não estiver logado, redireciona para a página inicial
    header('Location: index.php');
    exit;
}

// Verificar se o login foi bem-sucedido
$showSuccessPopup = false;
if (isset($_SESSION['login_success'])) {
    $showSuccessPopup = true;
    unset($_SESSION['login_success']); // Remove a variável para evitar exibições futuras
}

// Se o usuário já estiver logado, redireciona para a página de ambiente
if (isset($_SESSION['user_id'])) {
    header("Location: ambiente.php");
    exit();
}

// A sessão está ativa, obtém o e-mail e o nome do produtor
$produtor_email = $_SESSION['email'];
$produtor_nome = $_SESSION['nome'];
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="ico/SGE.ico" type="image/x-icon">
    <title>SGE - Ambiente do Produtor</title>
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
  />
    <link rel="stylesheet" href="ambiente.css">
   
    
</head>
<body>
      

    <header>
        
     <!-- Botão para abrir a sidebar -->
<button class="open-btn" onclick="abrirSidebar()">☰</button>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
    <span class="close-btn" onclick="fecharSidebar()">&times;</span> <!-- Botão "X" -->
    <h2 style="padding-left: 20px;">Menu</h2>
    &nbsp;
    <ul class="separator"> 
    <a href="lista de eventos.php">Eventos</a>
    &nbsp;
    <a href="colaboradores.php">Colaboradores</a>
    &nbsp;
    <ul> <a href="lista de staff.php">Staff</a></ul>
    &nbsp;
    <ul> <a href="Cargo.php">Cargo</a></ul>
    &nbsp;
    <ul> <a href="tema lista.php">Tema</a></ul>
    &nbsp;
    <ul> <a href="lista de buffet.php">Buffet</a></ul>
    &nbsp;
    <ul><a href="lista de objetivos.php">Objetivos</a> </ul>   
    &nbsp;
    <a href="relatório de problemas.php">Reportar Problemas</a>
    &nbsp;

</div>


<!-- Pop-up de Login Bem-Sucedido -->
<div id="loginSuccessPopup" class="popup" style="display: none;">
    <div class="popup-content">
     <span class="close-btn-popup" onclick="closePopup()">&times;</span> <!-- Botão "X" -->
        <h2>Login Bem-Sucedido</h2>
        <img src="correto.png" alt="Bem-vindo" class="popup-image">
        <p class= "bem-vindo">Bem-vindo(a), <?php echo htmlspecialchars($produtor_nome); ?>!</p>
    </div>
</div>

<script>
    // Verifica se o pop-up deve ser exibido
    const showSuccessPopup = <?php echo json_encode($showSuccessPopup); ?>;
    if (showSuccessPopup) {
        const popup = document.getElementById('loginSuccessPopup');
        popup.style.display = 'flex'; // Exibe o pop-up

        // Define um tempo para o pop-up desaparecer automaticamente (ex.: 3 segundos)
        setTimeout(() => {
            popup.style.display = 'none'; // Esconde o pop-up
        }, 3000); // 3000ms = 3 segundos
    }

    // Função para fechar o pop-up manualmente (opcional)
    function closePopup() {
        document.getElementById('loginSuccessPopup').style.display = 'none';
    }
</script>


        <div class="logo-foto"> 
           <img src="Logo_SGE_inova.png"width=80% height="100%">
           
       <div class="header-content"> 
      <h1> S.G.E.</h1> 
      <p> Sistema de Gestão de Eventos</p>
  
      </div>   
          </div>
  
          <div class="foto-container">
            <div class="logo">
              <img src="eventos.png"width=103% height="100%"> 
                 </div>
      
       </div>
      
   
          <nav> 
              
              <ul> 
                 
              <li><a href="ambiente.php" title="Página inicial">Home</a></li>  
        <li><a href="ajuda do produtor.php" title="Obtenha ajuda">Ajuda</a></li>
        <li><a href="Sobre.php" title="Sobre nós">Sobre</a></li>
                 
             </ul>
            
            </div>

                </nav>
                <div class="profile-dropdown">
                <div onclick="toggle()" class="profile-dropdown-btn">
               
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
            
            
        </svg>
     
                    
                
                <span
                    > 
                   <h1>Bem-vindo, <?php echo htmlspecialchars($produtor_nome); ?>! </h1> 
                   
                </span>
                </div>
               
                    <ul class="profile-dropdown-list">
                   
                      <li class="profile-dropdown-list-item">
                        <a href="listar produtores.php">
                          <i class="fa-regular fa-user"></i>
                          Gerenciar produtor
                        </a>
                      </li>
                      <li class="profile-dropdown-list-item">
                        <a href="perfil do produtor.php">
                          <i class="fa-regular fa-envelope"></i>
                           Editar Perfil
                        </a>
                      </li>
                      <li class="profile-dropdown-list-item">
                      <a onclick="showLogoutModal()">
                          <i class="fa-solid fa-arrow-right-from-bracket"></i>
                          Sair
                        </a>
                      </li>

                

<!-- Modal de Logout -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <h2>Deseja se deslogar?</h2>
        <!-- Formulário de logout -->
        <form id="logoutForm" method="POST" action="logo out.php">
            <!-- Botões Sim e Não em linha -->
            <div class="button-container">
                <!-- Botão Sim ativa a função JavaScript -->
                <button type="button" class="btn btn-yes" onclick="handleLogout()">Sim</button>

                <!-- Botão Não fecha o modal -->
                <button type="button" class="btn btn-no" onclick="closeLogout('logoutModal')">Não</button>
            </div>
        </form>
    </div>
</div>

<!-- Estilos CSS dos botões -->
<style>
    .button-container {
        display: flex; /* Usar flexbox para alinhar os botões */
        justify-content: space-between; /* Espaçamento entre os botões */
        gap: 10px; /* Espaço entre os botões */
    }
    .btn {
        padding: 10px 20px; /* Adiciona um espaçamento interno para os botões */
        cursor: pointer;
        border: none;
        font-size: 16px;
        border-radius: 5px;
    }
    .btn-yes {
        background-color: #4CAF50; /* Cor do botão Sim */
        color: white;
    }
    .btn-no {
        background-color: #f44336; /* Cor do botão Não */
        color: white;
    }
</style>


<!-- Modal de Agradecimento -->
<div id="thankYouModal" class="modal">
    <div class="modal-content">
        <h2>Obrigado por usar o nosso site!</h2>
       
    </div>
</div>

<script>
    // Função para mostrar o modal de logout
    function showLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    // Função para fechar qualquer modal
    function closeLogout(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Função para lidar com o logout com modal de agradecimento
    function handleLogout() {
        // Fecha o modal de logout
        closeLogout('logoutModal');

        // Mostra o modal de agradecimento
        document.getElementById('thankYouModal').style.display = 'flex';

        // Aguarda 2 segundos antes de enviar o formulário
        setTimeout(function() {
            document.getElementById('logoutForm').submit(); // Envia o formulário de logout
        }, 2000); // 2000 milissegundos = 2 segundos
    }
</script>




                      
                  
                    </ul>
                  </div>
                </nav>
              </li>
    
    </header>

    <section class="agenda-evento">
        
        <div class="conteudo">
          
           <div class="container"> 
               
          <h2>PRÓXIMOS EVENTOS</h2>

          
          <?php
include('php/Config.php');

// Função para exibir todos os eventos e contar o total de eventos
function exibirEventos() {
    global $conn;

    // Consulta para buscar todos os eventos cadastrados
    $sql_eventos = "SELECT e.nome, e.imagem, e.data, e.descricao, e.local, e.hora, e.lotacao, e.duracao,
                    fe.descricao AS faixa_etaria_desc, ss.descricao AS status_social_desc, 
                    se.nome AS status_evento_nome, es.descricao AS escolaridade_desc
                    FROM eventos e
                    LEFT JOIN faixa_etaria fe ON e.faixa_etaria_id = fe.id
                    LEFT JOIN status_social ss ON e.status_social_id = ss.id
                    LEFT JOIN status_do_evento se ON e.status_do_evento_id = se.id
                    LEFT JOIN escolaridades es ON e.escolaridades_id = es.id
                    ORDER BY e.data DESC";

    $result = $conn->query($sql_eventos);
    $totalEventos = $result->num_rows; // Conta o número total de eventos

    if ($totalEventos > 0) {
        while ($row = $result->fetch_assoc()) {
            $caminho_imagem = "uploads/eventos/" . htmlspecialchars($row['imagem']);

            // Conversão da duração de horas decimais para horas e minutos
            $duracao_horas = floatval($row['duracao']); // Recebe como horas (ex.: 1.5 para 1h 30min)
            $horas = floor($duracao_horas); // Parte inteira das horas
            $minutos = ($duracao_horas - $horas) * 60; // Calcula os minutos restantes

            // Formata a duração no formato "Xh Ymin"
            if ($horas > 0 && $minutos > 0) {
                $duracao_formatada = $horas . 'h ' . round($minutos) . 'min';
            } elseif ($horas > 0) {
                $duracao_formatada = $horas . 'h';
            } else {
                $duracao_formatada = round($minutos) . 'min';
            }

            // Exibindo os dados do evento
            echo '<div class="carousel-slide">';
            echo '<div class="evento">';
            echo '<h1>' . date("d/m/Y", strtotime($row['data'])) . '<br>' . htmlspecialchars($row['nome']) . '</h1>';

            // Verificando se a imagem existe
            if (!empty($row['imagem']) && file_exists($caminho_imagem)) {
                echo '<img src="' . $caminho_imagem . '" class="evento-imagem" alt="' . htmlspecialchars($row['nome']) . '" 
                        onmouseover="stopAutoSlide()" onmouseout="startAutoSlide()">';
            } else {
                echo '<p>Imagem não encontrada.</p>';
            }

            // Botão "Saiba Mais"
            echo '<button onmouseover="stopAutoSlide()" onmouseout="startAutoSlide()" onclick="showDetails(\'' . addslashes($row['nome']) . '\', \'' . addslashes($caminho_imagem) . '\', \'' . date("d/m/Y", strtotime($row['data'])) . '\', \'' . addslashes($row['descricao']) . '\', \'' . addslashes($row['local']) . '\', \'' . date("H\hi", strtotime($row['hora'])) . '\', \'' . addslashes($row['lotacao']) . '\', \'' . addslashes($duracao_formatada) . '\', \'' . addslashes($row['faixa_etaria_desc']) . '\', \'' . addslashes($row['status_social_desc']) . '\', \'' . addslashes($row['status_evento_nome']) . '\', \'' . addslashes($row['escolaridade_desc']) . '\')">Saiba Mais →</button>';

            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p>Nenhum evento encontrado.</p>";
    }

    return $totalEventos; // Retorna o total de eventos para o JavaScript
}
?>


<div class="eventos">
    <!-- Carrossel com 3 imagens -->
    <div class="carousel">
        <div class="carousel-container">
            <?php 
                $totalEventos = exibirEventos(); // Obtém o total de eventos
            ?>
        </div>
    </div>
    <!-- Botões de navegação -->
    <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
    <button class="next" onclick="moveSlide(1)">&#10095;</button>
</div>
         </div>
            </div>
            


<script> 
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const totalSlides = Math.ceil(slides.length / 3); // Calcular grupos de três

function showSlide(index) {
  if (index >= totalSlides) {
    currentSlide = 0; // Volta ao primeiro grupo de slides
  } else if (index < 0) {
    currentSlide = totalSlides - 1; // Vai para o último grupo
  } else {
    currentSlide = index;
  }

  // Ajustar o offset para mover três slides por vez
  const offset = -currentSlide * 100;
  document.querySelector('.carousel-container').style.transform = `translateX(${offset}%)`;
}

function moveSlide(direction) {
  showSlide(currentSlide + direction);
  resetAutoSlide(); // Reinicia o carrossel automático após interação manual
}

function startAutoSlide() {
  autoSlideInterval = setInterval(() => {
    moveSlide(1); // Avança automaticamente para o próximo grupo
  }, 5000); // Intervalo de 5 segundos
}

function stopAutoSlide() {
  clearInterval(autoSlideInterval);
}

function resetAutoSlide() {
  stopAutoSlide();
  startAutoSlide();
}

// Exibe o primeiro grupo ao carregar a página
showSlide(currentSlide);
startAutoSlide();
</script>

    
<!-- Modal -->
<div id="eventModal" class="modal-detalhes">
    <div class="modal-content-detalhes">
        <span class="close-btn-modal" onclick="closeModal()">&times;</span>
        <h2 id="modalNome"></h2>
        <p id="modalData"></p>
        <p id="modalDescricao"></p>
        <p><strong>Local:</strong> <span id="modalLocal"></span></p>
        <p><strong>Hora:</strong> <span id="modalHora"></span></p>
        <p><strong>Lotação:</strong> <span id="modalLotacao"></span></p>
        <p><strong>Duração:</strong> <span id="modalDuracao"></span></p>
        <p><strong>Faixa Etária:</strong> <span id="modalFaixaEtaria"></span></p>
        <p><strong>Status Social:</strong> <span id="modalStatusSocial"></span></p>
        <p><strong>Status do Evento:</strong> <span id="modalStatusEvento"></span></p>
        <p><strong>Escolaridade:</strong> <span id="modalEscolaridade"></span></p>
    </div>
</div>


    <script src="ambiente.js"></script>
</body>
</html>
