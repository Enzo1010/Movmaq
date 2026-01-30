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
loadComponent("header", "/public/components/header.html");
loadComponent("footer", "/public/components/footer.html");

// menu mobile
document.addEventListener("click", (e) => {
  if (e.target.id === "menuToggle") {
    document.getElementById("nav")?.classList.toggle("active");
  }
});

const map = L.map("map", {
  scrollWheelZoom: false,
}).setView([-14.235, -51.9253], 4);

L.tileLayer("https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {
  attribution: "&copy; OpenStreetMap & CartoDB",
}).addTo(map);

const locations = [
  { name: "São Paulo", coords: [-23.5505, -46.6333] },
  { name: "Curitiba", coords: [-25.4284, -49.2733] },
  { name: "Porto Alegre", coords: [-30.0346, -51.2177] },
  { name: "Belo Horizonte", coords: [-19.9167, -43.9345] },
  { name: "Rio de Janeiro", coords: [-22.9068, -43.1729] },
];

locations.forEach((loc) => {
  L.circleMarker(loc.coords, {
    radius: 9,
    color: "#003366",
    fillColor: "#ff6600",
    fillOpacity: 0.9,
    weight: 2,
  })
    .addTo(map)
    .bindPopup(
      `<div class="map-popup-title">${loc.name}</div>Base operacional`,
    );
});
