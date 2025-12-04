const form = document.getElementById("formAgendamento");
const msg = document.getElementById("msgRetorno");
const boxHorarios = document.getElementById("boxHorarios");

document.querySelectorAll(".btn-dia").forEach(btn => {
  btn.addEventListener("click", () => {
    document.getElementById("inputData").value = btn.dataset.dia;
    boxHorarios.style.display = "block";
  });
});

document.querySelectorAll(".hora").forEach(h => {
  h.addEventListener("click", () => {
    document.getElementById("inputHorario").value = h.dataset.hora;
    form.style.display = "block";
  });
});

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const dados = new FormData(form);

  const req = await fetch("salvar-agendamento.php", {
    method: "POST",
    body: dados
  });

  const res = await req.text();

  if (res === "OK") {
    msg.textContent = "✔️ Agendamento realizado com sucesso!";
    msg.style.color = "green";
  } else {
    msg.textContent = "❗ Erro ao agendar. Tente novamente.";
    msg.style.color = "red";
  }
});
