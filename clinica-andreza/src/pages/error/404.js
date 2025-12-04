document.getElementById("btn-voltar").addEventListener("click", () => {
    window.location.href = "/";
});

const heartContainer = document.getElementById("hearts-container");

function criarCoracao() {
    const heart = document.createElement("div");
    heart.classList.add("floating-heart");
    heart.textContent = "❤";

    // posição horizontal aleatória
    heart.style.left = Math.random() * 100 + "vw";

    // tamanho aleatório
    const size = Math.random() * 25 + 10; // entre 10 e 35px
    heart.style.fontSize = size + "px";

    // duração aleatória da animação
    const duracao = Math.random() * 5 + 5; // 5 a 10 segundos
    heart.style.animationDuration = duracao + "s";

    // girações leves para ficar bonito
    heart.style.transform = `rotate(${Math.random() * 40 - 20}deg)`;

    heartContainer.appendChild(heart);

    // remove o coração após animação
    setTimeout(() => {
        heart.remove();
    }, duracao * 1000);
}

// cria um coração a cada 400ms
setInterval(criarCoracao, 400);

// cria alguns corações iniciais
for (let i = 0; i < 15; i++) {
    setTimeout(criarCoracao, i * 200);
}
