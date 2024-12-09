<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

// Verificar se o login foi bem-sucedido
$showSuccessPopup = false;
if (isset($_SESSION['login_success'])) {
    $showSuccessPopup = true;
    unset($_SESSION['login_success']); // Remove a variável para evitar exibições futuras
}

 // Verificar se o usuário já está logado pela sessão
 if (isset($_SESSION['user_id'])) {
    // O usuário já está logado, redireciona para a página de sucesso
    header("Location: ambiente.php");
    exit();
}

    // Se o usuário já estiver logado através da sessão
    if (isset($_SESSION['user_id'])) {
        header("Location: ambiente.php");
        exit();
    }



$produtor_email = $_SESSION['email'];
$produtor_nome = $_SESSION['nome'];
?>

<?php


// Verifica se o parâmetro 'logout' foi passado na URL
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Destrua a sessão (fazendo logout)
    session_unset(); // Limpa todas as variáveis de sessão
    session_destroy(); // Destroi a sessão

    // Redireciona para a página de login ou homepage
    header("Location: index.php"); // Altere para a página de login ou onde desejar redirecionar
    exit();
}
// Sinaliza que o modal deve ser exibido
$showModal = true;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style> 
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500&display=swap");
:root {
  --primary: #eeeeee;
  --secondary: #290670;
  --green: #82cd47;
  --secondary-light: rgba(110, 28, 216, 0.726);
  --secondary-light-2: rgb(127, 183, 126, 0.1);
  --white: #fff;
  --black: #393e46;

  --shadow: 0px 2px 8px 0px var(--secondary-light);
}




.profile-dropdown {
    position: relative;
    width: fit-content;
    list-style: none;
    top: 10px;
    left: 35px;
  }

  .profile-dropdown-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.05rem 0.2rem;   /* Padding ainda menor */
    font-size: 0.6rem;         /* Tamanho de fonte menor */
    font-weight: 400;          /* Peso da fonte ajustado */
    width: 70%;               /* Largura ajustada ao conteúdo */
    border-radius: 20px;       /* Menor raio de borda */
    border: 1px solid var(--secondary); /* Borda fina */
    cursor: pointer;
    transition: box-shadow 0.2s ease-in, background-color 0.2s ease-in, border 0.
  }  
  
  
  .profile-dropdown-btn:hover {
    background-color: var(--secondary-light-2);
    box-shadow: var(--shadow);
  }
  .profile-img {
    position: relative;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: url(./assets/profile-pic.jpg);
    background-size: cover;
  }

  .profile-dropdown-btn span {
    margin: 0 0.5rem;
    margin-right: 0;
  }

  .profile-dropdown-btn span {
    margin: 0 0.5rem;
    margin-right: 10px;;
  }
  .profile-dropdown-list {
    position: absolute;
    top: 68px;
    width: 240px;
    right: 0;
    background-color: #5214cb;
    border-radius: 10px;
    max-height: 0;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: max-height 0.5s;
    list-style: none; /* Remove as bolinhas */
  }
  .profile-dropdown-list hr {
    border: 0.5px solid var(--green);
  }
  .profile-dropdown-list.active {
    max-height: 400px;
  }
  .profile-dropdown-list-item {
    padding: 0.5rem 0rem 0.5rem 1rem;
    transition: background-color 0.2s ease-in, padding-left 0.2s;
  }
  .profile-dropdown-list-item a {
    display: flex;
    align-items: center;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    color: white;
  }
  .profile-dropdown-list-item a i {
    margin-right: 0.8rem;
    font-size: 1.1rem;
    width: 2.3rem;
    height: 2.3rem;
    background-color: var(--secondary);
    color: var(--white);
    line-height: 2.3rem;
    text-align: center;
    margin-right: 1rem;
    border-radius: 50%;
    transition: margin-right 0.3s;
  }
  .profile-dropdown-list-item:hover {
    padding-left: 1.5rem;
    background-color: var(--secondary-light);
  }


    </style>
</head>
<body>

<header class="dropdown">

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
        <button class="btn btn-yes" onclick="confirmLogout()">Sim</button>
        <button class="btn btn-no" onclick="closeModal('logoutModal')">Não</button>
    </div>
</div>

<!-- Modal de Agradecimento -->
<div id="thankYouModal" class="modal">
    <div class="modal-content">
        <h2>Obrigado por usar o nosso site!</h2>
        <button class="btn btn-close" onclick="closeModal('thankYouModal')">Fechar</button>
    </div>
</div>

<script>
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
        }, 2000); // Aguarda 3 segundos antes de redirecionar
    }
</script>

<script>
let profileDropdownList = document.querySelector(".profile-dropdown-list");
let btn = document.querySelector(".profile-dropdown-btn");
let classList = profileDropdownList.classList;
const toggle = () => classList.toggle("active");
window.addEventListener("click", function (e) {
  if (!btn.contains(e.target)) classList.remove("active");
});
</script>
    
</body>
</html>