<?php
session_start();

include("php/Config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['senha'])) {
    if (!empty($_POST['email']) && !empty($_POST['senha'])) {
        // Capturar os dados do formulário
        $email = $_POST['email'];
        $senha = md5($_POST['senha']); // Criptografar a senha com MD5

        // Preparar a consulta SQL para a tabela de produtores de eventos
        $sql = "SELECT * FROM produtor WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        

        // Inclua na consulta SQL de inserção
        $sql = "INSERT INTO produtor (email, senha) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $hashed_password);


        // Verificar se o produtor existe
        if ($result && mysqli_num_rows($result) > 0) {
            $produtor = mysqli_fetch_assoc($result);

            // Verificar a senha
            if ($produtor['senha'] === $senha) {
                // Criar sessão para o produtor
                $_SESSION['id'] = $produtor['id'];
                $_SESSION['email'] = $produtor['email'];
                $_SESSION['nome'] = $produtor['nome']; // Exemplo: armazenar o nome do produtor

                if (isset($_POST['rememberMe'])) {
                    // Gerar um token único para identificar o usuário
                    $token = bin2hex(random_bytes(16)); // Token seguro e único
                
                    // Salvar o token no banco de dados, associado ao ID do usuário
                    $userId = $produtor['id'];
                    $updateTokenSql = "UPDATE produtor SET remember_token = '$token' WHERE id = $userId";
                    mysqli_query($conn, $updateTokenSql);
                
                    // Configurar cookies seguros
                    setcookie('remember_token', $token, time() + (86400 * 30), "/", "", false, true); // HTTP-Only
                }

                // Verificar se o cookie 'remember_token' está definido
                if (isset($_COOKIE['remember_token'])) {
                    $token = $_COOKIE['remember_token'];

                    // Procurar no banco de dados o usuário com esse token
                    $query = "SELECT * FROM produtor WHERE remember_token = '$token'";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        // Autenticar o usuário automaticamente
                        $produtor = mysqli_fetch_assoc($result);

                        // Definir a sessão do usuário (como se ele tivesse feito login manualmente)
                        $_SESSION['produtor_id'] = $produtor['id'];
                        $_SESSION['nome'] = $produtor['nome'];

                        // Redirecionar para a página inicial ou painel
                        header("Location: ambiente.php");
                        exit();
                    } else {
                        // Token inválido ou expirado
                        setcookie('remember_token', '', time() - 3600, "/", "", false, true); // Apagar o cookie
                    }
                }

                // Exibir o modal de sucesso
                $_SESSION['login_success'] = true;

                // Redirecionar para o painel do produtor
                header('Location: ambiente.php');
                exit;
            } else {
                // Senha incorreta - configurar a sessão para mostrar o modal
                $_SESSION['senha_incorreta'] = true;
            }
        } else {
             // Adicionar sessão para mostrar o pop-up de produtor não encontrado
           $_SESSION['produtor_nao_encontrado'] = true;
        }
    } else {
        echo "Por favor, preencha todos os campos.";
    }
} else {
   // echo "Acesso inválido.";
}

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="ico/SGE.ico" type="image/x-icon">
    <title>SGE - Agenda de Eventos</title>
    <link rel="stylesheet" href="index.css">
    <script src="index.js"> </script>
    

</head>

<body>

<header>
     
      <div class="logo-foto"> 
         <img src="Logo_SGE_inova.png"width=80% height="100%">
         
     <div class="header-content"> 
    <h1> S.G.E.</h1> 
    <p> Sistema de Gestão de Eventos</p>

    </div>   
        </div>

    

 <div class="logo">

     <img src="eventos.png"width=103% height="100%">
    
    </div>
    
 
        <nav> 
            
            <ul> 
               
        <li><a href="index.php" title="Página inicial">Home</a></li>  
        <li><a href="ajuda 1.php" title="Obtenha ajuda">Ajuda</a></li>
        <li><a href="Sobre 1.php" title="Sobre nós">Sobre</a></li>
        <a onclick="abrirPopUp()" title="Área Restrita">
        <li> <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
    </svg>
</a></li>

        </ul>
    </nav>
</header>

<!-- Overlay e Popup -->
<div id="overlay">
    <div id="popup">
        <p class="p">Área restrita ao produtor, deseja continuar?</p>
        <button class="btn-sim" onclick="openModal()">Sim</button>
        <button class="btn-nao" onclick="fecharPopUp()">Não</button>
    </div>
</div>

<!-- Modal de Login -->
<div id="loginModal" class="modal-login" style="display:none;">
    <section class="login-section-modal">
        <div class="login-box-modal"> 
            <h2> Fazer Login</h2>
            <a href="index.php" class="btn-close-login">&times;</a>
            <form action="" method="post">
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="senha">Senha:</label><br>
                    <input type="password" id="senha" name="senha" required>
                </div> 
                
                <button type="submit" class="login-btn">Entrar</button>
                <a href="index.php"><button type="button" class="Cancel-btn">Cancelar</button></a>
                <div class="circular-checkbox-wrapper">
                    <input type="checkbox" id="circular-checkbox" style="display: none;">
                    <label for="rememberMe">
                   <input type="checkbox" name="rememberMe" id="rememberMe" class="circular-checkbox"> Manter-me Conectado
                  </label>
                </div>

                <p><a href="validar_resposta.php">Esqueceu sua senha?</a></p>
            </form>
        </div>
    </section>
</div>

<!-- Modal de erro de senha -->
<div id="senhaIncorretaModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn-popup" onclick="fecharModal()">&times;</span>
        <h2>Erro!</h2>
        <p>Senha incorreta. Tente novamente.</p>
        <img src="erro login.png" alt="Erro">
    </div>
</div>

<!-- Modal de Produtor Não Encontrado -->
<div id="produtorNaoEncontradoModal" class="modal">
    <div class="modal-content">
        <span class="close-btn-popup" onclick="fecharModal('produtorNaoEncontradoModal')">&times;</span>
        <h2>Erro!</h2>
        <p>Produtor não encontrado. Verifique o email informado e tente novamente.</p>
        <img src="email nao encontrado.png" alt="Erro">
    </div>
</div>


<script> 
    // Função para abrir o pop-up
    function abrirPopUp() {
        document.getElementById("overlay").style.display = "flex";
    }

    // Função para fechar o pop-up
    function fecharPopUp() {
        document.getElementById("overlay").style.display = "none";
    }

    // Função para abrir o modal de login
    function openModal() {
        fecharPopUp();
        document.getElementById("loginModal").style.display = "block";
    }

    // Função para fechar o modal de login
    function fecharModal() {
        document.getElementById("senhaIncorretaModal").style.display = "none";
        document.getElementById("produtorNaoEncontradoModal").style.display = "none";         
        document.getElementById("loginModal").style.display = "block";
    }


    // Exibir o modal de erro de senha se a variável PHP for verdadeira
    <?php if (isset($_SESSION['senha_incorreta']) && $_SESSION['senha_incorreta'] === true): ?>
        document.getElementById("senhaIncorretaModal").style.display = "block";
        <?php unset($_SESSION['senha_incorreta']); ?>
    <?php endif; ?>

     // Exibir o modal de sucesso se o login foi bem-sucedido
     <?php if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true): ?>
        document.getElementById("modalSucesso").style.display = "block";
        <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    // Exibir o modal de produtor não encontrado
    <?php if (isset($_SESSION['produtor_nao_encontrado']) && $_SESSION['produtor_nao_encontrado'] === true): ?>
        document.getElementById("produtorNaoEncontradoModal").style.display = "block";
    <?php unset($_SESSION['produtor_nao_encontrado']); ?>
   <?php endif; ?>

    // Fechar o modal quando o usuário clicar fora dele
    window.onclick = function(event) {
        if (event.target == document.getElementById("senhaIncorretaModal")) {
            fecharModal();
        }

    }

       // Fechar o modal quando o usuário clicar fora dele
       window.onclick = function(event) {
        if (event.target == document.getElementById("produtorNaoEncontradoModal")) {
            fecharModal();
        }

    }
    
</script>
            </ul>

        </nav>
     
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
const carouselContainer = document.querySelector('.carousel-container');
let autoSlideInterval;

// Função para exibir o slide
function showSlide(index) {
  const totalSlides = Math.ceil(slides.length / 3); // Calcular grupos de três
  if (index >= totalSlides) {
    currentSlide = 0; // Volta ao primeiro grupo de slides
  } else if (index < 0) {
    currentSlide = totalSlides - 1; // Vai para o último grupo
  } else {
    currentSlide = index;
  }

  // Ajusta o offset para mover três slides por vez
  const offset = -currentSlide * 100;
  carouselContainer.style.transform = `translateX(${offset}%)`;

  // Atualiza a classe do contêiner para ajustar o alinhamento
  adjustCarouselClasses();
}

// Função para mover o slide na direção especificada
function moveSlide(direction) {
  showSlide(currentSlide + direction);
  resetAutoSlide(); // Reinicia o carrossel automático após interação manual
}

// Inicia o carrossel automático
function startAutoSlide() {
  autoSlideInterval = setInterval(() => {
    moveSlide(1); // Avança automaticamente para o próximo grupo
  }, 5000); // Intervalo de 5 segundos
}

// Para o carrossel automático
function stopAutoSlide() {
  clearInterval(autoSlideInterval);
}

// Reinicia o carrossel automático
function resetAutoSlide() {
  stopAutoSlide();
  startAutoSlide();
}

// Função para ajustar a classe do contêiner com base no número de slides visíveis
function adjustCarouselClasses() {
  const totalEvents = slides.length; // Número total de eventos
  let visibleSlides = totalEvents % 3 === 0 ? 3 : totalEvents % 3; // Determina a quantidade de eventos visíveis

  // Adiciona a classe apropriada para o contêiner
  carouselContainer.classList.remove('one-slide', 'two-slides', 'three-slides');

  if (visibleSlides === 1) {
    carouselContainer.classList.add('one-slide');
  } else if (visibleSlides === 2) {
    carouselContainer.classList.add('two-slides');
  } else {
    carouselContainer.classList.add('three-slides');
  }
}

// Exibe o primeiro grupo de slides ao carregar a página
showSlide(currentSlide);
startAutoSlide();
adjustCarouselClasses(); // Ajusta o alinhamento ao carregar

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

<script> 

function showDetails(nome, imagem, data, descricao, local, hora, lotacao, duracao, faixaEtaria, statusSocial, statusEvento, escolaridade) {
  // Parar o carrossel automático

  document.getElementById('modalNome').innerText = nome;
  document.getElementById('modalData').innerText = `Data: ${data}`;
  document.getElementById('modalDescricao').innerText = descricao;
  document.getElementById('modalLocal').innerText = local;
  document.getElementById('modalHora').innerText = hora;
  document.getElementById('modalLotacao').innerText = lotacao;
  document.getElementById('modalDuracao').innerText = duracao;
  document.getElementById('modalFaixaEtaria').innerText = faixaEtaria;
  document.getElementById('modalStatusSocial').innerText = statusSocial;
  document.getElementById('modalEscolaridade').innerText = escolaridade;

  const statusEventElement = document.getElementById('modalStatusEvento');
  statusEventElement.innerText = statusEvento;

  let statusClass = '';
  switch (statusEvento) {
      case 'Concluído':
          statusClass = 'status-concluido';
          break;
      case 'Cancelado':
          statusClass = 'status-cancelado';
          break;
      case 'Em Andamento':
          statusClass = 'status-ativo';
          break;
      case 'Adiado':
          statusClass = 'status-pendente';
          break;
      default:
          statusClass = '';
  }

  statusEventElement.className = statusClass;
  document.getElementById('eventModal').style.display = 'block';
}

function closeModal() {
  document.getElementById('eventModal').style.display = 'none';
  
}


window.onclick = function(event) {
    if (event.target == document.getElementById('eventModal')) {
        closeModal();
    }
}


</script>




</section>
</body>
</html>