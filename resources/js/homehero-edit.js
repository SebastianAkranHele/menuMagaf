document.addEventListener("DOMContentLoaded", () => {
    const previewTitle = document.getElementById("preview-title");
    const previewSubtitle = document.getElementById("preview-subtitle");
    const previewProfileTitle = document.getElementById("preview-profile-title");
    const previewProfileSubtitle = document.getElementById("preview-profile-subtitle");
    const previewProfile = document.getElementById("preview-profile");
    const heroPreview = document.getElementById("hero-preview");
    const previewButtons = document.getElementById("preview-buttons");

    // Atualiza textos do preview
    function bindInputToPreview(inputId, previewEl) {
        const input = document.getElementById(inputId);
        if (input && previewEl) {
            input.addEventListener("input", () => {
                previewEl.textContent = input.value || previewEl.dataset.placeholder || "";
            });
        }
    }

    bindInputToPreview("title", previewTitle);
    bindInputToPreview("subtitle", previewSubtitle);
    bindInputToPreview("profile_title", previewProfileTitle);
    bindInputToPreview("profile_subtitle", previewProfileSubtitle);

    // Atualiza imagem de perfil
    const profileInput = document.getElementById("profile_image");
    if (profileInput) {
        profileInput.addEventListener("change", e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => {
                    previewProfile.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Atualiza background do hero
    const bgInput = document.getElementById("background_image");
    if (bgInput) {
        bgInput.addEventListener("change", e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => {
                    heroPreview.style.backgroundImage = `url('${ev.target.result}')`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Adicionar/remover botões sociais
    const wrapper = document.getElementById("social-links-wrapper");
    const addBtn = document.getElementById("btn-add-link");

    if (addBtn && wrapper) {
        addBtn.addEventListener("click", () => {
            const index = wrapper.children.length;
            const div = document.createElement("div");
            div.classList.add("social-link-item");
            div.dataset.index = index;

            div.innerHTML = `
                <input type="text" name="social_links[${index}][name]" placeholder="Nome" class="form-control mb-1">
                <input type="url" name="social_links[${index}][url]" placeholder="URL" class="form-control mb-1">
                <input type="text" name="social_links[${index}][icon_class]" placeholder="Ícone (FontAwesome)" class="form-control mb-1">
                <input type="text" name="social_links[${index}][color_class]" placeholder="Cor (hex ou classe CSS)" class="form-control mb-1">
                <label><input type="checkbox" name="social_links[${index}][target_blank]"> Abrir em nova aba</label>
                <button type="button" class="btn-remove-link">❌ Remover</button>
            `;

            wrapper.appendChild(div);
            refreshPreviewButtons();
        });
    }

    // Remover botão social
    wrapper?.addEventListener("click", e => {
        if (e.target.classList.contains("btn-remove-link")) {
            e.target.closest(".social-link-item").remove();
            refreshPreviewButtons();
        }
    });

    // Atualizar botões sociais no preview
    function refreshPreviewButtons() {
        previewButtons.innerHTML = "";
        const items = wrapper.querySelectorAll(".social-link-item");
        if (items.length === 0) {
            previewButtons.innerHTML = `<span class="no-buttons">Nenhum botão social ainda</span>`;
            return;
        }

        items.forEach(item => {
            const name = item.querySelector(`input[name*='[name]']`)?.value || "Botão";
            const url = item.querySelector(`input[name*='[url]']`)?.value || "#";
            const icon = item.querySelector(`input[name*='[icon_class]']`)?.value || "fas fa-link";
            const color = item.querySelector(`input[name*='[color_class]']`)?.value || "#333";
            const target = item.querySelector(`input[name*='[target_blank]']`)?.checked ? "_blank" : "_self";

            const a = document.createElement("a");
            a.href = url;
            a.target = target;
            a.style.background = color;
            a.innerHTML = `<i class="${icon}"></i> ${name}`;

            previewButtons.appendChild(a);
        });
    }

    // Atualiza preview quando digita nos inputs de social links
    wrapper?.addEventListener("input", refreshPreviewButtons);
    wrapper?.addEventListener("change", refreshPreviewButtons);

    refreshPreviewButtons();
});
