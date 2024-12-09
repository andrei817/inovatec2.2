// Função para abrir a sidebar
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


  let profileDropdownList = document.querySelector(".profile-dropdown-list");
let btn = document.querySelector(".profile-dropdown-btn");
let classList = profileDropdownList.classList;
const toggle = () => classList.toggle("active");
window.addEventListener("click", function (e) {
  if (!btn.contains(e.target)) classList.remove("active");
});


function showDetails(nome, imagem, data, descricao, local, hora, lotacao, duracao, faixaEtaria, statusSocial, statusEvento, escolaridade) {
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

  
    

