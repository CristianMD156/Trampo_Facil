document.addEventListener("DOMContentLoaded", function () {
  const year = document.getElementById("year");
  if (year) year.textContent = new Date().getFullYear();

  verificarSessao();
  carregarVagas();

  // Eventos
  document.getElementById("btnEntrar")?.addEventListener("click", () => {});
  document.getElementById("doLogin")?.addEventListener("click", login);
  document
    .getElementById("doCadastro")
    ?.addEventListener("click", cadastrarUsuario);
  document.getElementById("btnSair")?.addEventListener("click", logout);
  document
    .getElementById("createJobBtn")
    ?.addEventListener("click", cadastrarVaga);
  document
    .getElementById("searchBtn")
    ?.addEventListener("click", pesquisarVagas);

  document
    .getElementById("btnCandidatePanel")
    ?.addEventListener("click", openCandidatePanel);
  document
    .getElementById("btnCompanyPanel")
    ?.addEventListener("click", openCompanyPanel);
  document
    .getElementById("btnOpenCandidate")
    ?.addEventListener("click", function () {
      const userStr = localStorage.getItem("usuarioLogado");
      if (!userStr) return;

      const u = JSON.parse(userStr);
      u.tipo === "empresa" ? openCompanyPanel() : openCandidatePanel();
    });

  document
    .getElementById("closeCandidate")
    ?.addEventListener(
      "click",
      () => (document.getElementById("candidatePanel").style.display = "none"),
    );
  document
    .getElementById("closeCompany")
    ?.addEventListener(
      "click",
      () => (document.getElementById("companyPanel").style.display = "none"),
    );
});

// SESSÃO E LOGIN
function verificarSessao() {
  const userStr = localStorage.getItem("usuarioLogado");
  const cardResumo = document.getElementById("cardResumo");
  const navBtns = {
    entrar: document.getElementById("btnEntrar"),
    sair: document.getElementById("btnSair"),
    cand: document.getElementById("btnCandidatePanel"),
    emp: document.getElementById("btnCompanyPanel"),
  };

  if (userStr) {
    const user = JSON.parse(userStr);
    navBtns.entrar.style.display = "none";
    navBtns.sair.style.display = "block";
    cardResumo.style.display = "block";

    document.getElementById("resumoNome").textContent = user.nome;
    document.getElementById("resumoCargo").textContent =
      user.tipo === "candidato" ? "Candidato" : "Empresa";
    document.getElementById("resumoAvatar").textContent = user.nome
      .split(" ")
      .map((n) => n[0])
      .join("")
      .toUpperCase()
      .substring(0, 2);

    user.tipo === "candidato"
      ? (navBtns.cand.style.display = "block")
      : (navBtns.emp.style.display = "block");
  } else {
    navBtns.entrar.style.display = "block";
    navBtns.sair.style.display = "none";
    cardResumo.style.display = "none";
    navBtns.cand.style.display = "none";
    navBtns.emp.style.display = "none";
  }
}

function login() {
  const email = document.getElementById("loginEmail").value;
  const senha = document.getElementById("loginPassword").value;

  fetch("backend/login.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, senha }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.erro) return alert(data.erro);
      localStorage.setItem("usuarioLogado", JSON.stringify(data));
      verificarSessao();
      bootstrap.Modal.getInstance(document.getElementById("loginModal")).hide();
    });
}

function logout() {
  localStorage.removeItem("usuarioLogado");
  verificarSessao();
}

function cadastrarUsuario() {
  const dados = {
    nome: document.getElementById("cadNome").value,
    email: document.getElementById("cadEmail").value,
    senha: document.getElementById("cadPassword").value,
    tipo: document.getElementById("accountType").value,
  };

  fetch("backend/cadastrar.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(dados),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.erro) alert(data.erro);
      else {
        alert("Conta criada! Pode entrar.");
        document.getElementById("login-tab").click();
      }
    });
}

// VAGAS
function carregarVagas() {
  fetch("backend/listar_vagas.php")
    .then((res) => res.json())
    .then((data) => {
      const container = document.getElementById("jobsList");
      container.innerHTML = "";
      data.forEach((v) => {
        container.innerHTML += `
                <div class="col-12"><div class="card p-3 shadow-sm job-card">
                    <h5>${v.titulo} <span class="badge bg-light text-dark">R$ ${v.salario}</span></h5>
                    <p class="text-muted small"><i class="bi bi-geo-alt"></i> ${v.cidade}</p>
                    <p>${v.descricao}</p>
                    <div class="text-end">
                        <button class="btn btn-sm btn-primary" onclick="candidatar('${v.id}')">Candidatar-se</button>
                    </div>
                </div></div>`;
      });
    });
}

function cadastrarVaga() {
  const user = JSON.parse(localStorage.getItem("usuarioLogado"));
  if (!user || user.tipo !== "empresa") return alert("Apenas empresas!");

  const dados = {
    empresa_id: user.id,
    titulo: prompt("Título:"),
    descricao: prompt("Descrição:"),
    salario: parseFloat(prompt("Salário:")) || 0,
    cidade: prompt("Cidade:"),
  };

  fetch("backend/criar_vaga.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(dados),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.erro) alert(data.erro);
      else {
        alert("Vaga criada!");
        carregarVagas();
        renderCompanyJobs();
      }
    });
}

function candidatar(idVaga) {
  const userStr = localStorage.getItem("usuarioLogado");

  if (!userStr) {
    alert("Você precisa entrar na sua conta para se candidatar!");
    const loginModal = new bootstrap.Modal(
      document.getElementById("loginModal"),
    );
    loginModal.show();
    return;
  }

  const user = JSON.parse(userStr);

  if (user.tipo !== "candidato") {
    return alert("Apenas contas de candidatos podem se inscrever nas vagas.");
  }

  fetch("backend/candidatar.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ candidato_id: user.id, vaga_id: idVaga }),
  })
    .then((res) => res.json())
    .then((data) => alert(data.erro || data.sucesso))
    .catch((err) => console.error("Erro ao candidatar:", err));
}

function openCompanyPanel() {
  document.getElementById("companyPanel").style.display = "block";
  renderCompanyJobs();
}

function renderCompanyJobs() {
  fetch("backend/listar_vagas.php")
    .then((res) => res.json())
    .then((data) => {
      const lista = document.getElementById("companyJobsList");
      lista.innerHTML = "";
      data.forEach(
        (v) =>
          (lista.innerHTML += `<li class="list-group-item">${v.titulo} - ${v.cidade}</li>`),
      );
    });
}

function pesquisarVagas() {
  const termo = document.getElementById("searchInput").value.toLowerCase();
  document.querySelectorAll("#jobsList .col-12").forEach((card) => {
    card.style.display = card.innerText.toLowerCase().includes(termo)
      ? "block"
      : "none";
  });
}

function openCandidatePanel() {
  document.getElementById("candidatePanel").style.display = "block";
  renderCandidateApplications(); // Chama a listagem quando o painel abre
}

function renderCandidateApplications() {
  const userStr = localStorage.getItem("usuarioLogado");
  if (!userStr) return;

  const user = JSON.parse(userStr);

  fetch(`backend/listar_candidaturas.php?usuario_id=${user.id}`)
    .then((res) => res.json())
    .then((data) => {
      const lista = document.getElementById("applicationsList");
      if (!lista) return;

      lista.innerHTML = "";

      if (data.erro) {
        lista.innerHTML = `<li class="list-group-item text-danger">${data.erro}</li>`;
        return;
      }

      if (data.length === 0) {
        lista.innerHTML = `<li class="list-group-item text-muted small">Você ainda não se candidatou a nenhuma vaga.</li>`;
        return;
      }

      data.forEach((cand) => {
        // Estiliza o status (Enviado fica cinza, Em análise fica azul, etc)
        let badgeClass =
          cand.status_candidatura === "enviado" ? "bg-secondary" : "bg-primary";

        lista.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${cand.titulo}</strong><br>
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> ${cand.cidade}</small>
                    </div>
                    <span class="badge ${badgeClass} text-uppercase">${cand.status_candidatura}</span>
                </li>
            `;
      });
    })
    .catch((err) => console.error("Erro ao carregar candidaturas:", err));
}
