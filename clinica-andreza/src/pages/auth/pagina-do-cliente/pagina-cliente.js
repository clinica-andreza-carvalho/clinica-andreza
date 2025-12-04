document.addEventListener("DOMContentLoaded", () => {

  const modal = document.getElementById("modalCancelamento");
  const btnsCancelar = document.querySelectorAll(".btn-cancelar");
  const btnFechar = document.getElementById("fecharModal");
  const btnConfirmar = document.getElementById("confirmarCancelamento");

  let agendamentoSelecionado = null;

  // Clicar em "Cancelar"
  btnsCancelar.forEach(btn => {
    btn.addEventListener("click", () => {
      agendamentoSelecionado = btn.closest("li");
      console.log("Abrindo modal..."); // DEBUG
      modal.classList.add("ativo");
    });
  });

  // Botão "Voltar"
  btnFechar.addEventListener("click", () => {
    modal.classList.remove("ativo");
  });

  // Botão "Sim, cancelar"
  btnConfirmar.addEventListener("click", () => {
    if (agendamentoSelecionado) {
      agendamentoSelecionado.remove();
    }
  
    modal.classList.remove("ativo");
  
    // abre o segundo modal
    document.getElementById("modalConfirmado").classList.add("ativo");
  });
  const modalConfirmado = document.getElementById("modalConfirmado");
  const btnFecharConfirmado = document.getElementById("fecharConfirmado");
  
  btnFecharConfirmado.addEventListener("click", () => {
    modalConfirmado.classList.remove("ativo");
  });
});
