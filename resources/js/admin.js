// resources/js/admin.js

class AdminMenu {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');

        // Criar botão hamburger
        this.menuToggle = document.querySelector('.sidebar-toggle');
        if (!this.menuToggle) {
            this.menuToggle = document.createElement('button');
            this.menuToggle.classList.add('sidebar-toggle');
            this.menuToggle.innerHTML = '☰';
            document.body.appendChild(this.menuToggle);
        }

        // Criar overlay
        this.sidebarOverlay = document.querySelector('.sidebar-overlay');
        if (!this.sidebarOverlay) {
            this.sidebarOverlay = document.createElement('div');
            this.sidebarOverlay.classList.add('sidebar-overlay');
            document.body.appendChild(this.sidebarOverlay);
        }

        // Criar botão de fechar dentro do sidebar
        this.sidebarClose = this.sidebar.querySelector('.sidebar-close');
        if (!this.sidebarClose) {
            this.sidebarClose = document.createElement('button');
            this.sidebarClose.classList.add('sidebar-close');
            this.sidebarClose.innerHTML = '✕';
            this.sidebar.appendChild(this.sidebarClose);
        }

        this.init();
    }

    init() {
        this.bindEvents();
        this.checkScreenSize();
        window.addEventListener('resize', () => this.checkScreenSize());
    }

    bindEvents() {
        // Abrir sidebar
        this.menuToggle.addEventListener('click', () => this.openSidebar());

        // Fechar sidebar
        this.sidebarClose.addEventListener('click', () => this.closeSidebar());
        this.sidebarOverlay.addEventListener('click', () => this.closeSidebar());

        // Fechar ao clicar em links (mobile)
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1024) this.closeSidebar();
            });
        });

        // ESC fecha
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closeSidebar();
        });
    }

    openSidebar() {
        this.sidebar.classList.add('active');
        this.sidebarOverlay.classList.add('active');
        document.body.classList.add('sidebar-open');

        // Ocultar hamburger
        this.menuToggle.style.display = 'none';
    }

    closeSidebar() {
        this.sidebar.classList.remove('active');
        this.sidebarOverlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');

        // Mostrar hamburger apenas em telas pequenas
        if (window.innerWidth <= 1024) {
            this.menuToggle.style.display = 'block';
        }
    }

    checkScreenSize() {
        if (window.innerWidth > 1024) {
            // Desktop: sidebar sempre visível, sem overlay nem botão hamburger
            this.sidebar.classList.remove('active');
            this.sidebarOverlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
            this.menuToggle.style.display = 'none';
        } else {
            // Mobile: sidebar fechada, botão hamburger visível
            this.sidebar.classList.remove('active');
            this.sidebarOverlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
            this.menuToggle.style.display = 'block';
        }
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    new AdminMenu();
});

