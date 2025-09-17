document.addEventListener("DOMContentLoaded", () => {
    console.log("Admin Home Index carregado ✅");

    // =======================
    // Animação do Preview da Home
    // =======================
    const preview = document.querySelector(".home-preview");
    if (preview) {
        preview.style.opacity = 0;
        preview.style.transform = "translateY(20px) scale(0.98)";
        setTimeout(() => {
            preview.style.transition = "all 0.6s cubic-bezier(0.22, 1, 0.36, 1)";
            preview.style.opacity = 1;
            preview.style.transform = "translateY(0) scale(1)";
        }, 100);
    }

    // =======================
    // Hover nos Links Sociais
    // =======================
    const socialLinks = document.querySelectorAll(".links-container a, .d-flex a");
    socialLinks.forEach(link => {
        link.style.transition = "all 0.3s ease";
        link.addEventListener("mouseenter", () => {
            link.style.boxShadow = "0 8px 20px rgba(0,0,0,0.15)";
            link.style.transform = "translateY(-2px)";
        });
        link.addEventListener("mouseleave", () => {
            link.style.boxShadow = "none";
            link.style.transform = "translateY(0)";
        });
    });

    // =======================
    // Hover no Botão de Edição
    // =======================
    const editBtn = document.querySelector(".edit-btn, .btn-lg.btn-danger");
    if (editBtn) {
        editBtn.style.transition = "all 0.3s ease";
        editBtn.addEventListener("mouseenter", () => {
            editBtn.style.boxShadow = "0 6px 18px rgba(0,0,0,0.2)";
            editBtn.style.transform = "translateY(-2px)";
        });
        editBtn.addEventListener("mouseleave", () => {
            editBtn.style.boxShadow = "none";
            editBtn.style.transform = "translateY(0)";
        });
    }

    // =======================
    // SweetAlert para remoção de links sociais
    // =======================
    document.body.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-link")) {
            e.preventDefault();

            Swal.fire({
                title: 'Tem certeza?',
                text: "Este link será removido!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const item = e.target.closest(".social-link-item");
                    if (item) {
                        item.style.transition = "opacity 0.4s ease, transform 0.4s ease";
                        item.style.opacity = "0";
                        item.style.transform = "translateX(-20px)";
                        setTimeout(() => item.remove(), 400);
                    }
                    Swal.fire('Removido!', 'O link foi removido.', 'success');
                }
            });
        }
    });

    // =======================
    // SweetAlert para remoção de imagens
    // =======================
    const bgCheckbox = document.getElementById("remove_background_image");
    if (bgCheckbox) {
        bgCheckbox.addEventListener("change", (e) => {
            if (e.target.checked) {
                Swal.fire({
                    title: 'Remover imagem de fundo?',
                    text: "A imagem será removida ao salvar.",
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                const img = e.target.closest(".mt-2")?.querySelector("img");
                if (img) {
                    img.style.transition = "opacity 0.4s ease";
                    img.style.opacity = "0.5";
                }
            }
        });
    }

    const profileCheckbox = document.getElementById("remove_profile_image");
    if (profileCheckbox) {
        profileCheckbox.addEventListener("change", (e) => {
            if (e.target.checked) {
                Swal.fire({
                    title: 'Remover imagem de perfil?',
                    text: "A imagem será removida ao salvar.",
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                const img = e.target.closest(".mt-2")?.querySelector("img");
                if (img) {
                    img.style.transition = "opacity 0.4s ease";
                    img.style.opacity = "0.5";
                }
            }
        });
    }
});
