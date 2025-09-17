// Efeito de digitação no título (opcional)
document.addEventListener('DOMContentLoaded', function() {
    // Animação de digitação para o título
    const title = document.querySelector('.logo h1');
    const originalText = title.textContent;
    title.textContent = '';
    
    let i = 0;
    const typeWriter = () => {
        if (i < originalText.length) {
            title.textContent += originalText.charAt(i);
            i++;
            setTimeout(typeWriter, 100);
        }
    };
    
    // Inicia a animação após um breve delay
    setTimeout(typeWriter, 500);
    
    // Adiciona classe de animação para os links quando estão em viewport
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, observerOptions);
    
    // Observa os elementos para animação
    document.querySelectorAll('.menu-link, .social-link').forEach(el => {
        observer.observe(el);
    });
    
    // Efeito de confirmação ao clicar nos links
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            // Se for um link externo, não faz nada
            if (this.getAttribute('target') === '_blank') return;
            
            e.preventDefault();
            this.classList.add('clicked');
            
            // Simula o redirecionamento após uma breve animação
            setTimeout(() => {
                window.location.href = this.href;
            }, 300);
        });
    });
});