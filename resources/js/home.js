document.addEventListener('DOMContentLoaded', function () {
    console.log("✅ home.js carregado com sucesso!");

    // ========== EFEITO DE DIGITAÇÃO ==========
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

    // ========== ANIMAÇÃO DE ENTRADA ==========
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('animated');
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.menu-link, .social-link').forEach(el => observer.observe(el));

    // ========== CLIQUE NOS LINKS ==========
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function (e) {
            if (this.getAttribute('target') === '_blank') return; // deixa abrir em nova aba
            e.preventDefault();
            this.classList.add('clicked');
            setTimeout(() => window.location.href = this.href, 300);
        });
    });

    // ========== MODAL ADMIN ==========
    const btnAdminAccess = document.getElementById('btnAdminAccess');
    const modal = document.getElementById('adminAccessModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const verifyCodeBtn = document.getElementById('verifyCodeBtn');
    const adminCodeInput = document.getElementById('adminCode');
    const errorMsg = document.getElementById('errorMsg');

    if (btnAdminAccess && modal) {
        // abrir modal
        btnAdminAccess.addEventListener('click', () => {
            modal.classList.remove('hidden');
            errorMsg.classList.add('hidden');
            adminCodeInput.value = '';
            adminCodeInput.focus();
        });

        // fechar modal
        closeModalBtn?.addEventListener('click', () => modal.classList.add('hidden'));

        // verificar código
        verifyCodeBtn?.addEventListener('click', async () => {
            const code = adminCodeInput.value.trim();
            if (!code) return;

            // 🔹 Abre a nova aba imediatamente (evita bloqueio por popup)
            const newTab = window.open('', '_blank');

            try {
                const response = await fetch('/check-access-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        newTab.location.href = '/admin/login';
                        modal.classList.add('hidden');
                    } else {
                        newTab.close();
                        errorMsg.classList.remove('hidden');
                    }
                } else {
                    newTab.close();
                    errorMsg.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Erro na verificação:', error);
                newTab.close();
                errorMsg.classList.remove('hidden');
            }
        });

        // ESC para fechar modal
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') modal.classList.add('hidden');
        });
    }

    // ========== ALERTA DE LOGOUT ==========
    const logoutAlert = document.getElementById('logout-alert');
    if (logoutAlert) {
        setTimeout(() => {
            logoutAlert.style.display = 'none';
        }, 3500);
    }
});
