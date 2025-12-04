//carousel 
const slides = document.querySelectorAll(".carousel-slide");
const prevBtn = document.querySelector(".carousel-btn.prev");
const nextBtn = document.querySelector(".carousel-btn.next");
const dots = document.querySelectorAll(".carousel-indicators .dot");
let currentIndex = 0;

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.classList.toggle("active", i === index);
  });

  dots.forEach((dot, i) => {
    dot.classList.toggle("active", i === index);
  });

  currentIndex = index;
}

function nextSlide() {
  const newIndex = (currentIndex + 1) % slides.length;
  showSlide(newIndex);
}

function prevSlide() {
  const newIndex = (currentIndex - 1 + slides.length) % slides.length;
  showSlide(newIndex);
}

//--------------------------------------------------------------//


// Eventos
nextBtn.addEventListener("click", nextSlide);
prevBtn.addEventListener("click", prevSlide);

dots.forEach(dot => {
  dot.addEventListener("click", () => {
    showSlide(parseInt(dot.getAttribute("data-index")));
  });
});

setInterval(nextSlide, 6000); 


const faqButtons = document.querySelectorAll(".faq-question");

faqButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    const currentlyActive = document.querySelector(".faq-question.active");

    if (currentlyActive && currentlyActive !== btn) {
      currentlyActive.classList.remove("active");
      currentlyActive.nextElementSibling.classList.remove("open");
    }

    btn.classList.toggle("active");
    btn.nextElementSibling.classList.toggle("open");
  });
});

console.log("testando")

//--------------------------------------------------------------//

//Link pag adm
async function carregarProdutos() {
  const res = await fetch("get_produtos.php");
  const produtos = await res.json();

  const container = document.getElementById("produtosLoja");

  container.innerHTML = produtos.map(p => `
    <div class="produto-item">
      <img src="${p.imagem}">
      <h3>${p.nome}</h3>
      <p>${p.descricao}</p>
      <strong>R$ ${p.preco}</strong>
    </div>
  `).join("");
}

carregarProdutos();
