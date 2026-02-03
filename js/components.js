async function loadComponent(id, file, afterLoad) {
  try {
    const response = await fetch(file);
    if (!response.ok) throw new Error(`Erro ao carregar ${file}`);

    const html = await response.text();
    document.getElementById(id).innerHTML = html;

    if (window.lucide) lucide.createIcons();
    if (window.AOS) AOS.refresh();

    if (typeof afterLoad === "function") afterLoad();
  } catch (error) {
    console.error(error);
  }
}

function setActiveMenu() {
  // pega o arquivo atual (index.html, services.html etc.)
  const currentFile = window.location.pathname.split("/").pop() || "index.html";

  // procura links do menu dentro do header carregado
  const links = document.querySelectorAll("#header nav a, #header a");

  links.forEach((link) => {
    const href = link.getAttribute("href");
    if (!href || href.startsWith("#") || href.startsWith("http")) return;

    const linkFile = href.split("/").pop();

    if (linkFile === currentFile) link.classList.add("active");
    else link.classList.remove("active");
  });
}

// carregando header e footer + marca menu ativo depois do header
loadComponent("header", "../../public/components/header.html", () => {
  setActiveMenu();
  if (typeof initHeaderUI === "function") initHeaderUI();
});
loadComponent("footer", "../../public/components/footer.html");

// menu mobile
document.addEventListener("click", (e) => {
  if (e.target.id === "menuToggle") {
    document.getElementById("nav")?.classList.toggle("active");
  }
});

