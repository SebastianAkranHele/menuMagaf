@extends('admin.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Página Inicial</h2>

    <form action="{{ route('admin.home.hero.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- ===== Hero Section ===== -->
        <div class="card mb-4">
            <div class="card-header">Hero</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $hero->title) }}">
                </div>
                <div class="mb-3">
                    <label for="subtitle" class="form-label">Subtítulo</label>
                    <input type="text" name="subtitle" id="subtitle" class="form-control" value="{{ old('subtitle', $hero->subtitle) }}">
                </div>

                <!-- Background Image -->
                <div class="mb-3">
                    <label class="form-label">Imagem de Fundo</label>
                    <select name="background_image_type" id="background_image_type" class="form-select mb-2">
                        <option value="file" {{ old('background_image_type', $hero->background_image_type ?? 'file') == 'file' ? 'selected' : '' }}>Arquivo local</option>
                        <option value="url" {{ old('background_image_type', $hero->background_image_type ?? '') == 'url' ? 'selected' : '' }}>URL externa</option>
                    </select>

                    <div id="background_image_file">
                        <input type="file" name="background_image" class="form-control">
                        @if($hero->background_image && ($hero->background_image_type ?? 'file') == 'file')
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$hero->background_image) }}" alt="Background atual" class="img-fluid" style="max-height:150px;">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_background_image" class="form-check-input" value="1">
                                    <label class="form-check-label">Remover imagem atual</label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div id="background_image_url" style="display: none;">
                        <input type="url" name="background_image_url" class="form-control" placeholder="https://exemplo.com/imagem.jpg" value="{{ old('background_image_url', ($hero->background_image_type ?? '') == 'url' ? $hero->background_image : '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Profile Section ===== -->
        <div class="card mb-4">
            <div class="card-header">Profile</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="profile_title" class="form-label">Título</label>
                    <input type="text" name="profile_title" id="profile_title" class="form-control" value="{{ old('profile_title', $hero->profile_title) }}">
                </div>
                <div class="mb-3">
                    <label for="profile_subtitle" class="form-label">Subtítulo</label>
                    <input type="text" name="profile_subtitle" id="profile_subtitle" class="form-control" value="{{ old('profile_subtitle', $hero->profile_subtitle) }}">
                </div>

                <!-- Profile Image -->
                <div class="mb-3">
                    <label class="form-label">Imagem de Perfil</label>
                    <select name="profile_image_type" id="profile_image_type" class="form-select mb-2">
                        <option value="file" {{ old('profile_image_type', $hero->profile_image_type ?? 'file') == 'file' ? 'selected' : '' }}>Arquivo local</option>
                        <option value="url" {{ old('profile_image_type', $hero->profile_image_type ?? '') == 'url' ? 'selected' : '' }}>URL externa</option>
                    </select>

                    <div id="profile_image_file">
                        <input type="file" name="profile_image" class="form-control">
                        @if($hero->profile_image && ($hero->profile_image_type ?? 'file') == 'file')
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$hero->profile_image) }}" alt="Profile atual" class="img-fluid" style="max-height:120px; border-radius:8px;">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_profile_image" class="form-check-input" value="1">
                                    <label class="form-check-label">Remover imagem atual</label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div id="profile_image_url" style="display: none;">
                        <input type="url" name="profile_image_url" class="form-control" placeholder="https://exemplo.com/imagem.jpg" value="{{ old('profile_image_url', ($hero->profile_image_type ?? '') == 'url' ? $hero->profile_image : '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Social Links Section ===== -->
        <div class="card mb-4">
            <div class="card-header">Links Sociais</div>
            <div class="card-body" id="social-links-wrapper">
                @php
                    $links = old('social_links', $hero->social_links ?? []);
                    $socialOptions = [
                        'facebook'  => ['label' => 'Facebook', 'icon' => 'fa-brands fa-facebook', 'color' => 'facebook-color'],
                        'instagram' => ['label' => 'Instagram', 'icon' => 'fa-brands fa-instagram', 'color' => 'instagram-color'],
                        'whatsapp'  => ['label' => 'WhatsApp', 'icon' => 'fa-brands fa-whatsapp', 'color' => 'whatsapp-color'],
                        'tiktok'    => ['label' => 'TikTok', 'icon' => 'fa-brands fa-tiktok', 'color' => 'tiktok-color'],
                    ];
                @endphp

                @forelse($links as $i => $link)
                    @php
                        $key = strtolower($link['name'] ?? '');
                        $opt = $socialOptions[$key] ?? ['icon' => '', 'label' => ucfirst($key)];
                    @endphp
                    <div class="border rounded p-3 mb-3 social-link-item">
                        <div class="mb-2">
                            <label class="form-label">Rede Social</label>
                            <select name="social_links[{{ $i }}][name]" class="form-select social-select">
                                <option value="">-- Selecione --</option>
                                @foreach($socialOptions as $sKey => $sOpt)
                                    <option value="{{ $sKey }}" data-icon="{{ $sOpt['icon'] }}" data-color="{{ $sOpt['color'] }}" {{ $key == $sKey ? 'selected' : '' }}>
                                        {{ $sOpt['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">URL</label>
                            <input type="url" name="social_links[{{ $i }}][url]" class="form-control" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="social_links[{{ $i }}][target_blank]" value="1" class="form-check-input" {{ !empty($link['target_blank']) ? 'checked' : '' }}>
                            <label class="form-check-label">Abrir em nova aba</label>
                        </div>
                        <div class="mt-2">
                            <i class="preview-icon {{ $opt['icon'] }}"></i>
                            <span class="preview-label">{{ $opt['label'] }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-link">Remover</button>
                    </div>
                @empty
                    <p class="text-muted">Nenhum link social adicionado ainda.</p>
                @endforelse

                <button type="button" class="btn btn-primary mt-3" id="add-social-link">+ Adicionar Link</button>
            </div>
        </div>

        <!-- ===== Botão Salvar ===== -->
        <div class="text-center">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form[action='{{ route('admin.home.hero.update') }}']");

    // Confirmar submit
    form.addEventListener("submit", function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja salvar as alterações?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, salvar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.isConfirmed){
                form.submit();
            }
        });
    });

    // ===== Social Links =====
    const wrapper = document.getElementById("social-links-wrapper");
    const addBtn = document.getElementById("add-social-link");

    function createSocialLinkItem(index) {
        return `
            <div class="border rounded p-3 mb-3 social-link-item">
                <div class="mb-2">
                    <label class="form-label">Rede Social</label>
                    <select name="social_links[${index}][name]" class="form-select social-select">
                        <option value="">-- Selecione --</option>
                        <option value="facebook" data-icon="fa-brands fa-facebook" data-color="facebook-color">Facebook</option>
                        <option value="instagram" data-icon="fa-brands fa-instagram" data-color="instagram-color">Instagram</option>
                        <option value="whatsapp" data-icon="fa-brands fa-whatsapp" data-color="whatsapp-color">WhatsApp</option>
                        <option value="tiktok" data-icon="fa-brands fa-tiktok" data-color="tiktok-color">TikTok</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">URL</label>
                    <input type="url" name="social_links[${index}][url]" class="form-control" placeholder="https://..." required>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="social_links[${index}][target_blank]" value="1" class="form-check-input">
                    <label class="form-check-label">Abrir em nova aba</label>
                </div>
                <div class="mt-2">
                    <i class="preview-icon"></i> <span class="preview-label"></span>
                </div>
                <button type="button" class="btn btn-sm btn-danger mt-2 remove-link">Remover</button>
            </div>
        `;
    }

    addBtn.addEventListener("click", () => {
        const index = wrapper.querySelectorAll(".social-link-item").length;
        wrapper.insertAdjacentHTML("beforeend", createSocialLinkItem(index));
    });

    wrapper.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-link")) {
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
                    e.target.closest(".social-link-item").remove();
                    Swal.fire('Removido!', 'O link foi removido.', 'success');
                }
            });
        }
    });

    wrapper.addEventListener("change", (e) => {
        if (e.target.classList.contains("social-select")) {
            const container = e.target.closest(".social-link-item");
            const previewIcon = container.querySelector(".preview-icon");
            const previewLabel = container.querySelector(".preview-label");
            const selected = e.target.selectedOptions[0];
            previewIcon.className = "preview-icon " + (selected?.dataset.icon || "");
            previewLabel.textContent = selected?.textContent || "";
        }
    });

    // ===== Toggle imagem: file ou URL =====
    const bgSelect = document.getElementById('background_image_type');
    const bgFile = document.getElementById('background_image_file');
    const bgUrl = document.getElementById('background_image_url');

    const profileSelect = document.getElementById('profile_image_type');
    const profileFile = document.getElementById('profile_image_file');
    const profileUrl = document.getElementById('profile_image_url');

    function toggleBg() {
        if(bgSelect.value === 'file') {
            bgFile.style.display = 'block';
            bgUrl.style.display = 'none';
        } else {
            bgFile.style.display = 'none';
            bgUrl.style.display = 'block';
        }
    }

    function toggleProfile() {
        if(profileSelect.value === 'file') {
            profileFile.style.display = 'block';
            profileUrl.style.display = 'none';
        } else {
            profileFile.style.display = 'none';
            profileUrl.style.display = 'block';
        }
    }

    bgSelect.addEventListener('change', toggleBg);
    profileSelect.addEventListener('change', toggleProfile);

    toggleBg();
    toggleProfile();
});
</script>

@endsection
