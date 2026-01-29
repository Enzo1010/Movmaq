async function loadComponent(id, file) {
  try {
    const response = await fetch(file);
    if (!response.ok) {
      throw new Error(`Erro ao carregar ${file}`);
    }

    const html = await response.text();
    document.getElementById(id).innerHTML = html;

    if (window.lucide) {
      lucide.createIcons();
    }

    if (window.AOS) {
      AOS.refresh();
    }
  } catch (error) {
    console.error(error);
  }
}

// carregar header e footer
loadComponent("header", "../../public/components/header.html");
loadComponent("footer", "../../public/components/footer.html");

// menu mobile
document.addEventListener("click", (e) => {
  if (e.target.id === "menuToggle") {
    document.getElementById("nav")?.classList.toggle("active");
  }
});
