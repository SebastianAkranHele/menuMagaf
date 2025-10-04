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
                        role="img" aria-label="QR Code para o menu digital da Garrafeira MAGAVI">
                    </canvas>

                    <!-- Botão de download -->
                    <a id="download-link" class="btn btn-danger" download="qrcode-magavi.png">
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
    const menuLink = "https://magaf.ao";
    const canvas = document.getElementById("qrcode");
    const downloadLink = document.getElementById("download-link");

    QRCode.toCanvas(canvas, menuLink, {
        width: 300,
        errorCorrectionLevel: 'H',
        color: { dark: "#800000", light: "#ffffff" }
    }, function (error) {
        if (error) console.error(error);
        else addLogoToQR();
        downloadLink.href = canvas.toDataURL("image/png");
    });

    function addLogoToQR() {
        const ctx = canvas.getContext("2d");
        const logo = new Image();
        logo.crossOrigin = "anonymous";
        logo.src = "https:/magaf.ao/menu-digital/assets/img/magaf1.jpg";

        logo.onload = function () {
            const logoSize = canvas.width * 0.2;
            const x = (canvas.width - logoSize) / 2;
            const y = (canvas.height - logoSize) / 2;

            ctx.fillStyle = "white";
            ctx.fillRect(x - 8, y - 8, logoSize + 16, logoSize + 16);
            ctx.drawImage(logo, x, y, logoSize, logoSize);

            downloadLink.href = canvas.toDataURL("image/png");
        };
    }
</script>
@endpush
