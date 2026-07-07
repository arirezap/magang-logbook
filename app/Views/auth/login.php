<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Logbook Magang PKTJ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff; /* White Background */
            color: #333333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e0e0e0; /* Gray Border */
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .brand-title {
            color: #0d47a1; /* Dominant Blue */
            font-weight: 700;
            margin-bottom: 5px;
            text-align: center;
        }

        .brand-subtitle {
            color: #757575; /* Gray Subtext */
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-control {
            border: 1px solid #ced4da;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #0d47a1;
            box-shadow: 0 0 0 0.25rem rgba(13, 71, 161, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: #424242; /* Darker Gray */
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: #0d47a1; /* Dominant Blue */
            border-color: #0d47a1;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #082d66;
            border-color: #082d66;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
        }

        .accent-line {
            height: 4px;
            background-color: #ffca28; /* Accent Yellow */
            width: 50px;
            margin: 0 auto 20px;
            border-radius: 2px;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <h3 class="brand-title">Logbook Magang</h3>
            <div class="accent-line"></div>
            <p class="brand-subtitle">Politeknik Keselamatan Transportasi Jalan</p>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert-error">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/login/process') ?>" method="POST">
                <div class="mb-3">
                    <label for="nomor_induk" class="form-label">Nomor Induk (NOTAR / NIP)</label>
                    <input type="text" class="form-control" id="nomor_induk" name="nomor_induk" placeholder="Masukkan Nomor Induk..." required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password..." required>
                </div>
                <button type="submit" class="btn btn-primary">Masuk ke Sistem</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
