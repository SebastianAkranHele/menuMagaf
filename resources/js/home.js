document.addEventListener('DOMContentLoaded', function () {
    console.log("✅ home.js carregado com sucesso!");

    // ======== EFEITO DE DIGITAÇÃO ========
    const title = document.querySelector('.logo h1');
    if (title) {
        const originalText = title.textContent.trim();
        title.textContent = '';
        let i = 0;
        const typeWriter = () => {
            if (i < originalText.length) {
                title.textContent += originalText.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };
        setTimeout(typeWriter, 500);
    }

    // ======== ANIMAÇÃO DE ENTRADA ========
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('animated');
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.menu-link, .social-link').forEach(el => observer.observe(el));

    // ======== CLIQUE NOS LINKS ========
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function (e) {
            if (this.getAttribute('target') === '_blank') return;
            e.preventDefault();
            this.classList.add('clicked');
            setTimeout(() => window.location.href = this.href, 300);
        });
    });

    // ======== ALERTA DE LOGOUT ========
    const logoutAlert = document.getElementById('logout-alert');
    if (logoutAlert) {
        setTimeout(() => {
            logoutAlert.style.display = 'none';
        }, 3500);
    }
});
