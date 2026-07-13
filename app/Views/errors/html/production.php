<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Sistem | PKTJ Logbook</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            max-width: 600px;
            width: 90%;
            border-top: 5px solid #dc3545;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 0.5rem;
            text-shadow: 4px 4px 0px rgba(220, 53, 69, 0.1);
        }
        .error-icon {
            font-size: 4rem;
            color: #ffca28;
            margin-bottom: 1rem;
        }
        .btn-custom {
            background-color: #0b2545;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-custom:hover {
            background-color: #1a56db;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 86, 219, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="bi bi-exclamation-triangle-fill error-icon"></i>
        <div class="error-code">500</div>
        <h2 class="fw-bold mb-3">Terjadi Kesalahan Sistem</h2>
        <p class="text-muted mb-4" style="font-size: 1.1rem;">
            Maaf, tampaknya sedang ada gangguan teknis pada server kami. Teknisi kami sedang berupaya memperbaikinya secepat mungkin. Silakan coba beberapa saat lagi.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <button onclick="window.location.reload()" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold border-2">
                <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang
            </button>
            <a href="<?= base_url('/') ?>" class="btn-custom">
                <i class="bi bi-house-door-fill"></i> Ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
