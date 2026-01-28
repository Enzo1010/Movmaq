// Inicializar AOS (Anima??es) - APENAS SE EXISTIR

if (typeof AOS !== 'undefined') {

    AOS.init({

        duration: 1000,

        once: true,

        offset: 100

    });

}



// Inicializar Swiper (Slider) - APENAS SE EXISTIR

if (document.querySelector('.heroSwiper') && typeof Swiper !== 'undefined') {

    const swiper = new Swiper('.heroSwiper', {

        loop: true,

        autoplay: {

            delay: 5000,

            disableOnInteraction: false,

        },

        navigation: {

            nextEl: '.swiper-button-next',

            prevEl: '.swiper-button-prev',

        },

        pagination: {

            el: '.swiper-pagination',

            clickable: true,

        },

        effect: 'fade',

        fadeEffect: {

            crossFade: true

        }

    });

}



// Menu Mobile

const mobileMenuBtn = document.getElementById('mobileMenuBtn');

const nav = document.getElementById('nav');



if (mobileMenuBtn) {

    mobileMenuBtn.addEventListener('click', () => {

        nav.classList.toggle('active');

        mobileMenuBtn.classList.toggle('active');

    });



    // Fechar menu ao clicar em um link

    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {

        link.addEventListener('click', () => {

            nav.classList.remove('active');

            mobileMenuBtn.classList.remove('active');

        });

    });

}



// Scroll suave para ?ncoras

document.querySelectorAll('a[href^="#"]').forEach(anchor => {

    anchor.addEventListener('click', function (e) {

        const href = this.getAttribute('href');

        if (href !== '#' && href.length > 1) {

            e.preventDefault();

            const target = document.querySelector(href);

            if (target) {

                target.scrollIntoView({

                    behavior: 'smooth',

                    block: 'start'

                });

            }

        }

    });

});



// Header scroll effect

let lastScroll = 0;

const header = document.querySelector('.header');

const topBanner = document.querySelector('.top-banner');

const utilityBar = document.querySelector('.utility-bar');



if (header) {

    window.addEventListener('scroll', () => {

        const currentScroll = window.pageYOffset;

        

        if (currentScroll > 100) {

            header.style.boxShadow = '0 5px 20px rgba(0,0,0,0.15)';

        } else {

            header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';

        }

        

        lastScroll = currentScroll;

    });

}



// Fechar barra superior

const bannerCloseBtn = document.querySelector('.banner-close');

if (bannerCloseBtn && topBanner) {

    bannerCloseBtn.addEventListener('click', () => {

        topBanner.style.display = 'none';

    });

}



// Dropdown menu para mobile (robusto em resize)

const dropdowns = document.querySelectorAll('.dropdown');



dropdowns.forEach(dropdown => {

    const link = dropdown.querySelector('.nav-link');

    const megaMenu = dropdown.querySelector('.mega-menu');

    if (!link || !megaMenu) return;



    link.addEventListener('click', (e) => {

        // S? intercepta clique em modo mobile

        if (!window.matchMedia('(max-width: 1024px)').matches) return;



        e.preventDefault();

        megaMenu.style.display = megaMenu.style.display === 'block' ? 'none' : 'block';

    });

});



// Resetar estado inline do mega-menu quando voltar para desktop

window.addEventListener('resize', () => {

    if (window.matchMedia('(min-width: 1025px)').matches) {

        document.querySelectorAll('.dropdown .mega-menu').forEach(menu => {

            menu.style.display = '';

        });

    }

});



// ----------------------------------------

// FORMUL?RIO DE CONTATO - PHP

// ----------------------------------------



const contactForm = document.getElementById('contactForm');



if (contactForm) {

    contactForm.addEventListener('submit', async (e) => {

        e.preventDefault();

        

        // Pegar bot?o de submit

        const submitBtn = contactForm.querySelector('.btn-submit');

        const originalText = submitBtn.textContent;

        

        // Mostrar loading

        submitBtn.textContent = 'Enviando...';

        submitBtn.disabled = true;

        

        // Pegar dados do formul?rio

        const formData = new FormData(contactForm);

        

        try {

            // Enviar para o PHP

            const response = await fetch('send-email.php', {

                method: 'POST',

                body: formData

            });

            

            const result = await response.json();

            

            if (result.success) {

                // Sucesso

                showSuccessMessage(formData.get('name'));

                contactForm.reset();

            } else {

                // Erro retornado pelo PHP

                showErrorMessage(result.message);

            }

            

        } catch (error) {

            // Erro de conex?o

            console.error('Erro:', error);

            showErrorMessage('Erro ao enviar mensagem. Por favor, tente novamente ou entre em contato por telefone.');

            

        } finally {

            // Restaurar bot?o

            submitBtn.textContent = originalText;

            submitBtn.disabled = false;

        }

    });

}



// Funo para mostrar mensagem de sucesso

function showSuccessMessage(name) {

    const message = document.createElement('div');

    message.className = 'alert-message success-message';

    message.innerHTML = `

        <div class="alert-content">

            <div class="alert-icon">?</div>

            <div class="alert-text">

                <h3>Mensagem Enviada com Sucesso!</h3>

                <p>Obrigado, <strong>${name}</strong>! Sua mensagem foi recebida e entraremos em contato em breve.</p>

            </div>

            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">?</button>

        </div>

    `;

    

    document.body.appendChild(message);

    

    // Remover aps 5 segundos

    setTimeout(() => {

        message.remove();

    }, 5000);

    

    // Scroll para o topo

    window.scrollTo({ top: 0, behavior: 'smooth' });

}



// Funo para mostrar mensagem de erro

function showErrorMessage(errorMsg) {

    const message = document.createElement('div');

    message.className = 'alert-message error-message';

    message.innerHTML = `

        <div class="alert-content">

            <div class="alert-icon">?</div>

            <div class="alert-text">

                <h3>Erro ao Enviar Mensagem</h3>

                <p>${errorMsg}</p>

                <p>Ou ligue: <a href="tel:18666011758">1-866-601-1758</a></p>

            </div>

            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">?</button>

        </div>

    `;

    

    document.body.appendChild(message);

    

    // Remover aps 7 segundos

    setTimeout(() => {

        message.remove();

    }, 7000);

}



// Adicionar estilos para as mensagens

const alertStyles = document.createElement('style');

alertStyles.textContent = `

    .alert-message {

        position: fixed;

        top: 20px;

        right: 20px;

        max-width: 400px;

        background: white;

        border-radius: 10px;

        box-shadow: 0 10px 40px rgba(0,0,0,0.3);

        z-index: 10000;

        animation: slideInRight 0.3s ease;

    }

    

    .alert-content {

        display: flex;

        gap: 15px;

        padding: 20px;

        position: relative;

    }

    

    .alert-icon {

        width: 40px;

        height: 40px;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 24px;

        font-weight: bold;

        flex-shrink: 0;

    }

    

    .success-message .alert-icon {

        background: #10b981;

        color: white;

    }

    

    .error-message .alert-icon {

        background: #ef4444;

        color: white;

    }

    

    .alert-text h3 {

        margin: 0 0 8px 0;

        font-size: 16px;

        color: #333;

    }

    

    .alert-text p {

        margin: 0;

        font-size: 14px;

        color: #666;

        line-height: 1.5;

    }

    

    .alert-text a {

        color: #dc2626;

        font-weight: 600;

        text-decoration: none;

    }

    

    .alert-close {

        position: absolute;

        top: 10px;

        right: 10px;

        background: none;

        border: none;

        font-size: 24px;

        color: #999;

        cursor: pointer;

        padding: 0;

        width: 24px;

        height: 24px;

        line-height: 1;

    }

    

    .alert-close:hover {

        color: #333;

    }

    

    @keyframes slideInRight {

        from {

            transform: translateX(400px);

            opacity: 0;

        }

        to {

            transform: translateX(0);

            opacity: 1;

        }

    }

    

    @media (max-width: 768px) {

        .alert-message {

            top: 10px;

            right: 10px;

            left: 10px;

            max-width: none;

        }

    }

`;

document.head.appendChild(alertStyles);



// Lazy loading de imagens

if ('IntersectionObserver' in window) {

    const imageObserver = new IntersectionObserver((entries, observer) => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {

                const img = entry.target;

                if (img.dataset.src) {

                    img.src = img.dataset.src;

                    img.classList.add('loaded');

                    observer.unobserve(img);

                }

            }

        });

    });



    const images = document.querySelectorAll('img[data-src]');

    images.forEach(img => imageObserver.observe(img));

}



console.log('Site carregado com sucesso!');

console.log('Formul?rio configurado com PHP!');



