<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Mueblería Elegance ERP</title>
    
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-orange: #ff7a18;
            --primary-dark: #151928;
            --primary-navy: #1e2438;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0e121e;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 24px;
        }

        h1, h2, h3, h4, h5, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        .login-card-container {
            width: 100%;
            max-width: 1050px;
            background: #171c2c;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-wrap: wrap;
        }

        .login-banner-side {
            flex: 1.1;
            min-width: 320px;
            background: linear-gradient(145deg, #131726 0%, #1a2238 50%, #201a2d 100%);
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        .login-banner-side::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 122, 24, 0.15) 0%, rgba(255, 122, 24, 0) 70%);
            pointer-events: none;
        }

        .banner-logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-box {
            width: 48px;
            height: 48px;
            background: #ffffff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #151928;
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
        }

        .banner-feature-list {
            list-style: none;
            padding: 0;
            margin: 32px 0;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 14.5px;
            color: #cbd5e1;
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 122, 24, 0.15);
            color: var(--primary-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .login-form-side {
            flex: 1;
            min-width: 340px;
            background: #1c2134;
            padding: 48px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-control-custom {
            background-color: #131726;
            border: 1px solid #2a3148;
            color: #ffffff;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            background-color: #161b2c;
            border-color: var(--primary-orange);
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 122, 24, 0.18);
            outline: none;
        }

        .form-control-custom::placeholder {
            color: #64748b;
        }

        .btn-login-submit {
            background: linear-gradient(135deg, #ff7a18 0%, #ff5200 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 8px 20px rgba(255, 122, 24, 0.35);
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(255, 122, 24, 0.45);
            color: #ffffff;
        }

        .demo-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }

        .demo-chip:hover {
            background: rgba(255, 122, 24, 0.15);
            border-color: var(--primary-orange);
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="login-card-container">
    <!-- LEFT BANNER -->
    <div class="login-banner-side">
        <div>
            <div class="banner-logo mb-4">
                <div class="logo-box">.D</div>
                <div>
                    <h4 class="mb-0 fw-bold text-white">Mueblería Elegance</h4>
                    <span style="color: var(--primary-orange); font-size: 12px; letter-spacing: 1px; font-weight: 600; text-transform: uppercase;">
                        Sistema de Gestión Integral
                    </span>
                </div>
            </div>

            <div class="mt-4">
                <span class="badge px-3 py-2 rounded-pill text-white mb-3" style="background: rgba(255, 122, 24, 0.2); border: 1px solid rgba(255, 122, 24, 0.4);">
                    <i class="fa-solid fa-gem me-1 text-warning"></i> Plataforma Comercial & Taller
                </span>
                <h2 class="fw-bold text-white mb-2" style="font-size: 30px; line-height: 1.2;">
                    Control total para tu negocio de muebles y diseño
                </h2>
                <p class="text-secondary" style="font-size: 14px;">
                    Desde el stock con medidas y maderas en fábrica hasta la entrega y armado en el domicilio del cliente.
                </p>
            </div>

            <ul class="banner-feature-list">
                <li class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-ruler-combined"></i></div>
                    <div>
                        <strong>Catálogo con Dimensiones y Materiales</strong>
                        <div class="text-secondary" style="font-size: 12.5px;">Roble, Cedro, Melamina, Tapices Lino y Cuero</div>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <div>
                        <strong>Despacho, Envíos y Armado</strong>
                        <div class="text-secondary" style="font-size: 12.5px;">Trazabilidad de entrega con asignación de repartidores</div>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                    <div>
                        <strong>KPIs y Finanzas en Tiempo Real</strong>
                        <div class="text-secondary" style="font-size: 12.5px;">Caja diaria, gastos operativos y balance de utilidad</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="text-secondary" style="font-size: 12px;">
            © 2026 Mueblería & Diseño Elegance • Todos los derechos reservados.
        </div>
    </div>

    <!-- RIGHT LOGIN FORM -->
    <div class="login-form-side">
        <div class="mb-4">
            <h3 class="fw-bold text-white mb-1">Acceso al Sistema</h3>
            <p class="text-secondary" style="font-size: 14px;">Ingresa tus credenciales para continuar al Dashboard.</p>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3 border-0 rounded-3 text-danger" style="background: rgba(239, 68, 68, 0.15); font-size: 13px;">
                <i class="fa-solid fa-circle-exclamation me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info py-2 px-3 mb-3 border-0 rounded-3 text-info" style="background: rgba(59, 130, 246, 0.15); font-size: 13px;">
                <i class="fa-solid fa-circle-info me-1"></i>
                {{ session('info') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label text-light fw-semibold" style="font-size: 13px;">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background: #131726; color: #64748b; border-radius: 12px 0 0 12px;">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="emailInput" class="form-control form-control-custom" style="border-radius: 0 12px 12px 0;" placeholder="admin@muebleria.com" value="{{ old('email', 'admin@muebleria.com') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label text-light fw-semibold mb-0" style="font-size: 13px;">Contraseña</label>
                    <a href="#" class="text-decoration-none" style="font-size: 12px; color: var(--primary-orange);">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background: #131726; color: #64748b; border-radius: 12px 0 0 12px;">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="passwordInput" class="form-control form-control-custom" style="border-radius: 0;" placeholder="••••••••" value="admin123" required>
                    <button class="btn btn-outline-secondary border-0" type="button" id="togglePasswordBtn" style="background: #131726; color: #64748b; border-radius: 0 12px 12px 0;">
                        <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked style="background-color: #131726; border-color: #2a3148;">
                    <label class="form-check-label text-secondary" for="rememberMe" style="font-size: 13px;">
                        Recordar dispositivo
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-login-submit mb-4">
                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Iniciar Sesión
            </button>
        </form>

        <!-- Accesos Rápidos Demo (1-Click) -->
        <div class="pt-3 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
            <div class="text-secondary fw-semibold mb-2" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                Cuentas de demostración (Clic para rellenar):
            </div>
            <div class="d-flex flex-column gap-2">
                <div class="demo-chip" onclick="fillCredentials('admin@muebleria.com', 'admin123')">
                    <i class="fa-solid fa-crown text-warning me-1"></i>
                    <strong>Administrador:</strong> admin@muebleria.com / admin123
                </div>
                <div class="demo-chip" onclick="fillCredentials('vendedor@muebleria.com', 'vendedor123')">
                    <i class="fa-solid fa-bag-shopping text-info me-1"></i>
                    <strong>Ventas:</strong> vendedor@muebleria.com / vendedor123
                </div>
                <div class="demo-chip" onclick="fillCredentials('almacen@muebleria.com', 'almacen123')">
                    <i class="fa-solid fa-boxes-stacked text-success me-1"></i>
                    <strong>Almacén:</strong> almacen@muebleria.com / almacen123
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Eye toggle for password
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('passwordInput');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    togglePasswordBtn.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        togglePasswordIcon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });

    // Quick credentials filler
    function fillCredentials(email, pass) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = pass;
    }
</script>

</body>
</html>
