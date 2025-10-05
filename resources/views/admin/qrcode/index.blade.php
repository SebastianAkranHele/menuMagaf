@extends('admin.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow text-center">
                <div class="card-body">
                    <h3 class="card-title text-danger">Menu Digital 🍷</h3>
                    <p class="text-muted">Escaneie o QR Code para aceder ao menu</p>

                    <!-- QR Code -->
                    <canvas id="qrcode" class="img-fluid mx-auto d-block mb-3"
                        role="img" aria-label="QR Code para o menu digital da Garrafeira MAGAF">
                    </canvas>

                    <!-- Botão de download -->
                    <a id="download-link" class="btn btn-danger" download="qrcode-magaf.png">
                        📥 Baixar QR Code
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    const menuLink = "https://magaf.ao"; // Página inicial
    const canvas = document.getElementById("qrcode");
    const downloadLink = document.getElementById("download-link");

    // Gerar QR Code
    QRCode.toCanvas(canvas, menuLink, {
        width: 300,
        errorCorrectionLevel: 'H',
        color: { dark: "#800000", light: "#ffffff" }
    }, function (error) {
        if (error) console.error(error);
        else addLogoToQR();
    });

    // Adicionar o logotipo no centro
    function addLogoToQR() {
        const ctx = canvas.getContext("2d");
        const logo = new Image();
        logo.crossOrigin = "anonymous";
        logo.src = "{{ asset('assets/magaf1.jpg') }}"; // Caminho correto da imagem no public/assets

        logo.onload = function () {
            const logoSize = canvas.width * 0.2; // Tamanho proporcional ao QR
            const x = (canvas.width - logoSize) / 2;
            const y = (canvas.height - logoSize) / 2;

            // Fundo branco atrás do logo (opcional)
            ctx.fillStyle = "white";
            ctx.fillRect(x - 8, y - 8, logoSize + 16, logoSize + 16);

            // Inserir o logo
            ctx.drawImage(logo, x, y, logoSize, logoSize);

            // Atualizar link de download
            downloadLink.href = canvas.toDataURL("image/png");
        };
    }
</script>
@endpush
