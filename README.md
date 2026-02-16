# Movmaq

Projeto institucional para o setor de solucoes industriais (portas, docas de carga, manutencao e equipamentos).

## Tecnologias

- HTML5
- CSS3
- JavaScript (vanilla)
- PHP (endpoint de formulario)
- Bibliotecas: Swiper, AOS e Lucide Icons

## Estrutura

```text
Movmaq/
|-- assets/
|   |-- css/
|   `-- images/
|-- backend/
|   `-- send-email.php
|-- js/
|   |-- components.js
|   `-- script.js
|-- public/
|   |-- components/
|   `-- pages/
|-- .gitignore
`-- README.md
```

## Como rodar localmente

Recomendado usar servidor local para evitar erro de `fetch` no formulario.

1. Abra o terminal na raiz do projeto.
2. Inicie um servidor PHP:

```bash
php -S localhost:8000
```

3. Abra no navegador:

```text
http://localhost:8000/public/pages/index.html
```

## Formulario de contato

- Frontend: `public/pages/contact.html`
- Script JS: `js/script.js`
- Backend: `backend/send-email.php`

## Status

- [x] Estrutura base de paginas
- [x] Integracao com Swiper e AOS
- [x] Formulario com validacao no backend
- [ ] Conteudo final do cliente (textos e imagens)
- [ ] Ajustes finais de responsividade e SEO
