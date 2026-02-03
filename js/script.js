// AOS (animation) - init if available
if (typeof AOS !== 'undefined') {
  AOS.init({ duration: 1000, once: true, offset: 100 });
}

// Hero Swiper - init if available
if (document.querySelector('.heroSwiper') && typeof Swiper !== 'undefined') {
  new Swiper('.heroSwiper', {
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    pagination: { el: '.swiper-pagination', clickable: true },
    effect: 'fade',
    fadeEffect: { crossFade: true }
  });
}

function initHeaderUI() {
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const nav = document.getElementById('nav');

  if (mobileMenuBtn && nav && !mobileMenuBtn.dataset.binded) {
    mobileMenuBtn.dataset.binded = 'true';

    mobileMenuBtn.addEventListener('click', () => {
      nav.classList.toggle('active');
      mobileMenuBtn.classList.toggle('active');
    });

    nav.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('active');
        mobileMenuBtn.classList.remove('active');
      });
    });
  }

  const header = document.querySelector('.header');

  if (header && !header.dataset.shadowBound) {
    header.dataset.shadowBound = 'true';
    const toggleShadow = () => {
      const scrolled = window.pageYOffset;
      header.style.boxShadow =
        scrolled > 100
          ? '0 5px 20px rgba(0,0,0,0.15)'
          : '0 2px 10px rgba(0,0,0,0.1)';
    };

    toggleShadow();
    window.addEventListener('scroll', toggleShadow);
  }

  const topBanner = document.querySelector('.top-banner');
  const bannerCloseBtn = document.querySelector('.banner-close');

  if (topBanner && bannerCloseBtn && !bannerCloseBtn.dataset.binded) {
    bannerCloseBtn.dataset.binded = 'true';
    bannerCloseBtn.addEventListener('click', () => {
      topBanner.style.display = 'none';
    });
  }

  document.querySelectorAll('.dropdown').forEach(dropdown => {
    if (dropdown.dataset.binded) return;
    dropdown.dataset.binded = 'true';

    const link = dropdown.querySelector('.nav-link');
    const megaMenu = dropdown.querySelector('.mega-menu');
    if (!link || !megaMenu) return;

    link.addEventListener('click', event => {
      if (!window.matchMedia('(max-width: 1024px)').matches) return;
      event.preventDefault();
      megaMenu.style.display = megaMenu.style.display === 'block' ? 'none' : 'block';
    });
  });
}

// Smooth anchor scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', event => {
    const href = anchor.getAttribute('href');
    if (href === '#' || href.length <= 1) return;
    event.preventDefault();
    const target = document.querySelector(href);
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});


// Reset mega-menu inline state on desktop
window.addEventListener('resize', () => {
  if (window.matchMedia('(min-width: 1025px)').matches) {
    document.querySelectorAll('.dropdown .mega-menu').forEach(menu => {
      menu.style.display = '';
    });
  }
});

// Contact form
const contactForm = document.getElementById('contactForm');

if (contactForm) {
  contactForm.addEventListener('submit', async event => {
    event.preventDefault();

    const submitBtn = contactForm.querySelector('.btn-submit');
    const originalText = submitBtn ? submitBtn.textContent : '';

    if (submitBtn) {
      submitBtn.textContent = 'Enviando...';
      submitBtn.disabled = true;
    }

    const formData = new FormData(contactForm);

    try {
      const response = await fetch('send-email.php', { method: 'POST', body: formData });
      const result = await response.json();

      if (result.success) {
        showSuccessMessage(formData.get('name') || '');
        contactForm.reset();
      } else {
        showErrorMessage(result.message || 'Nao foi possivel enviar sua mensagem.');
      }
    } catch (error) {
      console.error('Erro:', error);
      showErrorMessage('Erro ao enviar mensagem. Tente novamente ou ligue para 1-866-601-1758.');
    } finally {
      if (submitBtn) {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    }
  });
}

function showSuccessMessage(name) {
  const message = createAlert({
    title: 'Mensagem enviada com sucesso!',
    body: `Obrigado, <strong>${name}</strong>! Sua mensagem foi recebida e entraremos em contato em breve.`,
    type: 'success'
  });

  document.body.appendChild(message);
  setTimeout(() => message.remove(), 5000);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showErrorMessage(errorMsg) {
  const message = createAlert({
    title: 'Erro ao enviar mensagem',
    body: `${errorMsg}<br>Ou ligue: <a href="tel:18666011758">1-866-601-1758</a>`,
    type: 'error'
  });

  document.body.appendChild(message);
  setTimeout(() => message.remove(), 7000);
}

function createAlert({ title, body, type }) {
  const wrapper = document.createElement('div');
  wrapper.className = `alert-message ${type}-message`;
  wrapper.innerHTML = `
    <div class="alert-content">
      <div class="alert-icon">${type === 'success' ? 'OK' : '!'}</div>
      <div class="alert-text">
        <h3>${title}</h3>
        <p>${body}</p>
      </div>
      <button class="alert-close" aria-label="Fechar aviso">&times;</button>
    </div>
  `;

  wrapper.querySelector('.alert-close').addEventListener('click', () => wrapper.remove());
  return wrapper;
}

// Add alert styles once
if (!document.querySelector('style[data-alert-styles]')) {
  const alertStyles = document.createElement('style');
  alertStyles.setAttribute('data-alert-styles', 'true');
  alertStyles.textContent = `
    .alert-message {
      position: fixed;
      top: 20px;
      right: 20px;
      max-width: 400px;
      background: #fff;
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
      font-size: 18px;
      font-weight: 700;
      flex-shrink: 0;
    }
    .success-message .alert-icon { background: #10b981; color: #fff; }
    .error-message .alert-icon { background: #ef4444; color: #fff; }
    .alert-text h3 {
      margin: 0 0 8px;
      font-size: 16px;
      color: #333;
      text-transform: capitalize;
    }
    .alert-text p {
      margin: 0;
      font-size: 14px;
      color: #555;
      line-height: 1.5;
    }
    .alert-text a { color: #dc2626; font-weight: 600; text-decoration: none; }
    .alert-close {
      position: absolute;
      top: 10px;
      right: 10px;
      background: none;
      border: none;
      font-size: 20px;
      color: #999;
      cursor: pointer;
      padding: 0;
      width: 24px;
      height: 24px;
      line-height: 1;
    }
    .alert-close:hover { color: #333; }
    @keyframes slideInRight {
      from { transform: translateX(400px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
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
}

// Lazy loading for images
if ('IntersectionObserver' in window) {
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const img = entry.target;
      if (img.dataset.src) {
        img.src = img.dataset.src;
        img.classList.add('loaded');
        observer.unobserve(img);
      }
    });
  });

  document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));
}

