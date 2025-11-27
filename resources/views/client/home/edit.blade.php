@extends('client.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Página Inicial</h2>

    @php
        $hero->background_image_type = $hero->background_image_type ?? 'file';
        $hero->profile_image_type = $hero->profile_image_type ?? 'file';
    @endphp

    <a href="{{ url('/'.$client->slug.'/menu') }}" class="btn btn-secondary mb-3" target="_blank">
        Visualizar Menu Público
    </a>

    <form action="{{ route('client.home.update') }}" method="POST" enctype="multipart/form-data">
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
                        <option value="file" {{ $hero->background_image_type == 'file' ? 'selected' : '' }}>Arquivo local</option>
                        <option value="url" {{ $hero->background_image_type == 'url' ? 'selected' : '' }}>URL externa</option>
                    </select>

                    <div id="background_image_file">
                        <input type="file" name="background_image" class="form-control">
                        @if($hero->background_image && $hero->background_image_type == 'file')
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$hero->background_image) }}" class="img-fluid" style="max-height:150px;">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_background_image" class="form-check-input" value="1">
                                    <label class="form-check-label">Remover imagem atual</label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div id="background_image_url" style="display: none;">
                        <input type="url" name="background_image_url" class="form-control" placeholder="https://exemplo.com/imagem.jpg" value="{{ $hero->background_image_type == 'url' ? $hero->background_image : '' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Profile Section ===== -->
        <div class="card mb-4">
            <div class="card-header">Perfil</div>
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
                        <option value="file" {{ $hero->profile_image_type == 'file' ? 'selected' : '' }}>Arquivo local</option>
                        <option value="url" {{ $hero->profile_image_type == 'url' ? 'selected' : '' }}>URL externa</option>
                    </select>

                    <div id="profile_image_file">
                        <input type="file" name="profile_image" class="form-control">
                        @if($hero->profile_image && $hero->profile_image_type == 'file')
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$hero->profile_image) }}" class="img-fluid rounded" style="max-height:120px;">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_profile_image" class="form-check-input" value="1">
                                    <label class="form-check-label">Remover imagem atual</label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div id="profile_image_url" style="display: none;">
                        <input type="url" name="profile_image_url" class="form-control" placeholder="https://exemplo.com/imagem.jpg" value="{{ $hero->profile_image_type == 'url' ? $hero->profile_image : '' }}">
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

                <template id="social-link-template">
                    <div class="border rounded p-3 mb-3 social-link-item">
                        <div class="mb-2">
                            <label class="form-label">Rede Social</label>
                            <select name="social_links[__INDEX__][name]" class="form-select social-select">
                                <option value="">-- Selecione --</option>
                                @foreach($socialOptions as $key => $opt)
                                    <option value="{{ $key }}" data-icon="{{ $opt['icon'] }}" data-color="{{ $opt['color'] }}">
                                        {{ $opt['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">URL</label>
                            <input type="url" name="social_links[__INDEX__][url]" class="form-control" placeholder="https://..." required>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="social_links[__INDEX__][target_blank]" value="1" class="form-check-input">
                            <label class="form-check-label">Abrir em nova aba</label>
                        </div>
                        <div class="mt-2">
                            <i class="preview-icon"></i> <span class="preview-label"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-link">Remover</button>
                    </div>
                </template>

                @foreach($links as $i => $link)
                    <div class="border rounded p-3 mb-3 social-link-item">
                        <div class="mb-2">
                            <label class="form-label">Rede Social</label>
                            <select name="social_links[{{ $i }}][name]" class="form-select social-select">
                                <option value="">-- Selecione --</option>
                                @foreach($socialOptions as $key => $opt)
                                    <option value="{{ $key }}" data-icon="{{ $opt['icon'] }}" data-color="{{ $opt['color'] }}" {{ strtolower($link['name'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $opt['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">URL</label>
                            <input type="url" name="social_links[{{ $i }}][url]" class="form-control" value="{{ $link['url'] ?? '' }}">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="social_links[{{ $i }}][target_blank]" value="1" class="form-check-input" {{ !empty($link['target_blank']) ? 'checked' : '' }}>
                            <label class="form-check-label">Abrir em nova aba</label>
                        </div>
                        <div class="mt-2">
                            <i class="preview-icon {{ $socialOptions[strtolower($link['name'] ?? '')]['icon'] ?? '' }}"></i>
                            <span class="preview-label">{{ $socialOptions[strtolower($link['name'] ?? '')]['label'] ?? ucfirst($link['name'] ?? '') }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-link">Remover</button>
                    </div>
                @endforeach

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

    // Confirmar submit
    const form = document.querySelector("form[action='{{ route('client.home.update') }}']");
    form.addEventListener("submit", e => {
        e.preventDefault();
        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja salvar as alterações?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, salvar',
            cancelButtonText: 'Cancelar'
        }).then(result => { if(result.isConfirmed) form.submit(); });
    });

    // Toggle de imagens
    const toggleField = (select, fileDiv, urlDiv) => {
        select.addEventListener('change', () => {
            if(select.value === 'file') { fileDiv.style.display='block'; urlDiv.style.display='none'; }
            else { fileDiv.style.display='none'; urlDiv.style.display='block'; }
        });
        select.dispatchEvent(new Event('change'));
    };
    toggleField(document.getElementById('background_image_type'), document.getElementById('background_image_file'), document.getElementById('background_image_url'));
    toggleField(document.getElementById('profile_image_type'), document.getElementById('profile_image_file'), document.getElementById('profile_image_url'));

    // Social Links
    const wrapper = document.getElementById("social-links-wrapper");
    const template = document.getElementById("social-link-template").innerHTML;

    document.getElementById("add-social-link").addEventListener("click", () => {
        const index = wrapper.querySelectorAll(".social-link-item").length;
        wrapper.insertAdjacentHTML("beforeend", template.replace(/__INDEX__/g, index));
    });

    wrapper.addEventListener("click", e => {
        if(e.target.classList.contains("remove-link")){
            Swal.fire({
                title:'Tem certeza?',
                text:'Este link será removido!',
                icon:'warning',
                showCancelButton:true,
                confirmButtonColor:'#d33',
                cancelButtonColor:'#3085d6',
                confirmButtonText:'Sim, remover',
                cancelButtonText:'Cancelar'
            }).then(result => { if(result.isConfirmed) e.target.closest(".social-link-item").remove(); });
        }
    });

    wrapper.addEventListener("change", e => {
        if(e.target.classList.contains("social-select")){
            const container = e.target.closest(".social-link-item");
            const previewIcon = container.querySelector(".preview-icon");
            const previewLabel = container.querySelector(".preview-label");
            const selected = e.target.selectedOptions[0];
            previewIcon.className = "preview-icon " + (selected?.dataset.icon || "");
            previewLabel.textContent = selected?.textContent || "";
        }
    });

});
</script>
@endsection
