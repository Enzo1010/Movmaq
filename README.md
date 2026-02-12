# Movmaq

Este projeto é um desenvolvimento freelance para um cliente no setor de soluções industriais (portas, docas de carga, manutenção, etc.). O design e estrutura são inspirados no site [Northern Dock Systems](https://www.northerndocksystems.com/).

> **Nota**: O projeto encontra-se em estágio de desenvolvimento e aguarda o envio dos dados reais (textos e imagens) por parte do cliente. Atualmente, utiliza placeholders e estruturas baseadas na referência.

## 🛠 Tecnologias Utilizadas

O projeto foi construído utilizando tecnologias web padrão, sem dependência de frameworks complexos, garantindo leveza e facilidade de manutenção.

- **HTML5**: Semântico e estruturado.
- **CSS3**: Estilização customizada (`assets/css/style.css`).
- **JavaScript**: Lógica de interação e componentes (`js/script.js`, `js/components.js`).
- **Bibliotecas Externas**:
  - [Swiper.js](https://swiperjs.com/): Para os carrosséis/sliders (Hero section).
  - [AOS (Animate On Scroll)](https://michalsnik.github.io/aos/): Para animações de entrada dos elementos.
  - [Lucide Icons](https://lucide.dev/): Para ícones vetoriais leves.

## 📂 Estrutura do Projeto

```
Movmaq/
├── .vscode/              # Configurações do editor
├── assets/
│   ├── css/              # Folhas de estilo (style.css)
│   └── images/           # Imagens e ícones
├── backend/
│   └── send-email.php    # Script PHP para envio de formulários
├── js/
│   ├── components.js     # Lógica para carregar header/footer
│   └── script.js         # Scripts gerais da aplicação
├── public/
│   ├── components/       # Componentes HTML reutilizáveis
│   │   ├── footer.html
│   │   └── header.html
│   └── pages/            # Páginas do site
│       ├── about.html
│       ├── contact.html
│       ├── index.html
│       ├── products.html
│       ├── services.html
│       └── success.html
├── .gitattributes
├── .gitignore
└── README.md
```

## 🚀 Como Executar

Como é um projeto estático, não há necessidade de instalação de dependências ou build tools complexos.

1. Navegue até a pasta `public/pages/`.
2. Abra o arquivo `index.html` em seu navegador de preferência.

## 🚧 Status do Projeto

- [x] Estrutura inicial do HTML
- [x] Integração com Swiper e AOS
- [ ] Conteúdo final do cliente (Textos e Imagens)
- [ ] Ajustes finos de responsividade
- [ ] Implementação de formulários de contato

---
*Desenvolvido como projeto freelance.*
