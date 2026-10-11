<?php
session_start();
require 'conexion.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR']; // Obtener IP del usuario

    // Buscar el usuario por email[cite: 17]
    $stmt = $pdo->prepare("SELECT id_usuario, clave, id_rol FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['clave'])) {
        // Credenciales correctas: Guardar log de ÉXITO[cite: 17]
        $log = $pdo->prepare("INSERT INTO accesos (id_usuario, ip_usuario, resultado) VALUES (?, ?, 'exito')");
        $log->execute([$user['id_usuario'], $ip]);

        // Crear variables de sesión
        $_SESSION['usuario_id'] = $user['id_usuario'];
        $_SESSION['rol_id'] = $user['id_rol'];

        // Redirigir según el rol[cite: 17]
        if ($user['id_rol'] == 1) { // 1 = Admin
            header("Location: admin.php");
        } else { // 2 = Usuario
            header("Location: dashboard.php");
        }
        exit;
    } else {
        // Credenciales incorrectas[cite: 17]
        // Si el usuario existe, pero puso mal la clave, guardamos el log de FALLO
        if ($user) {
            $log = $pdo->prepare("INSERT INTO accesos (id_usuario, ip_usuario, resultado) VALUES (?, ?, 'fallido')");
            $log->execute([$user['id_usuario'], $ip]);
        }
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#202326">
	<title>Acceso | Daytona Ciberconciencia</title>
	<style>
		:root {
			color-scheme: dark;
			--ink: #202326;
			--panel: #292d31;
			--panel-edge: rgba(255, 255, 255, .2);
			--muted: #b7bdc1;
			--orange: #f58a12;
			--orange-light: #ffad42;
			--field: #343a3f;
		}

		* { box-sizing: border-box; }

		body {
			min-width: 320px;
			min-height: 100vh;
			margin: 0;
			display: grid;
			place-items: center;
			overflow-x: hidden;
			padding: 32px 20px;
			color: #f4f5f5;
			background:
				radial-gradient(ellipse at 50% 48%, rgba(245, 138, 18, .1), transparent 34%),
				linear-gradient(115deg, #383d40, #222629 54%, #1d2022);
			font-family: "Trebuchet MS", "Segoe UI", sans-serif;
		}

		body::before {
			position: fixed;
			inset: 0;
			z-index: -1;
			content: "";
			opacity: .2;
			background-image: linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
			background-size: 42px 42px;
			mask-image: linear-gradient(90deg, black, transparent 43%, transparent 57%, black);
		}

		.backdrop {
			position: fixed;
			inset: 0;
			display: grid;
			grid-template-columns: minmax(220px, 1fr) minmax(300px, 1.25fr);
			gap: 22px;
			align-content: center;
			padding: 5vw;
			opacity: .34;
			filter: blur(2px);
			pointer-events: none;
		}

		.metrics { display: grid; gap: 20px; align-content: center; }

		.metric-panel, .terminal-panel {
			border: 1px solid rgba(225, 230, 230, .34);
			border-radius: 12px;
			background: rgba(223, 226, 224, .85);
			color: #24292b;
			box-shadow: 0 18px 50px rgba(0, 0, 0, .2);
		}

		.metric-panel { min-height: 190px; padding: 22px; }
		.metric-panel strong { font-size: 13px; font-weight: 600; }
		.bars { height: 130px; display: flex; align-items: end; justify-content: space-around; gap: 9px; padding-top: 18px; }
		.bars i { width: 8%; border-radius: 4px 4px 0 0; background: #565e61; }
		.bars i:nth-child(1) { height: 34%; }
		.bars i:nth-child(2) { height: 62%; }
		.bars i:nth-child(3) { height: 48%; }
		.bars i:nth-child(4) { height: 76%; }
		.bars i:nth-child(5) { height: 57%; }
		.bars i:nth-child(6) { height: 92%; }
		.bars i:nth-child(7) { height: 68%; }
		.donut { width: 108px; height: 108px; margin: 18px auto 0; border: 20px solid #555d60; border-right-color: #a7aca9; border-bottom-color: #777e80; border-radius: 50%; }

		.terminal-panel {
			min-height: 410px;
			align-self: center;
			overflow: hidden;
			padding: 24px;
			border-color: rgba(255, 255, 255, .12);
			background: rgba(29, 33, 35, .88);
			color: #9da6a5;
			font: 12px/2 "Consolas", monospace;
		}

		.terminal-dots { display: flex; gap: 7px; margin-bottom: 18px; }
		.terminal-dots i { width: 8px; height: 8px; border-radius: 50%; background: #e27c21; }
		.terminal-dots i:nth-child(2) { background: #d5b24c; }
		.terminal-dots i:nth-child(3) { background: #668e63; }
		.terminal-panel p { margin: 5px 0; }
		.terminal-panel span { color: #d1a461; }

		.login-card {
			position: relative;
			width: min(100%, 464px);
			padding: 34px 56px 32px;
			border: 1px solid var(--panel-edge);
			border-radius: 18px;
			background: rgba(39, 43, 47, .97);
			box-shadow: 0 24px 70px rgba(0, 0, 0, .42), 0 0 32px rgba(245, 138, 18, .1);
			animation: enter .55s ease-out both;
		}

		.brand { margin: 0 auto 22px; text-align: center; }
		.brand-mark { width: 58px; height: 58px; margin: 0 auto 8px; color: var(--orange); filter: drop-shadow(0 0 10px rgba(245, 138, 18, .25)); }
		.brand-name { margin: 0; color: var(--orange-light); font-size: 27px; font-weight: 800; line-height: 1; letter-spacing: .7px; }
		.brand-subtitle { margin: 4px 0 0; color: #f4a235; font-size: 13px; font-weight: 700; letter-spacing: 1.6px; }

		h1 { margin: 0; text-align: center; font-size: 21px; font-weight: 500; }
		.intro { max-width: 310px; margin: 9px auto 25px; color: #d8dcdd; text-align: center; font-size: 15px; line-height: 1.45; }
		.field { margin-top: 16px; }
		label { display: block; margin-bottom: 7px; color: #f1f2f2; font-size: 14px; font-weight: 600; }

		input {
			width: 100%;
			min-height: 44px;
			padding: 0 13px;
			border: 1px solid #596167;
			border-radius: 8px;
			outline: none;
			background: var(--field);
			color: #fff;
			font: inherit;
			transition: border-color .18s ease, box-shadow .18s ease;
		}

		input::placeholder { color: #929a9e; }
		input:focus { border-color: var(--orange); box-shadow: 0 0 0 3px rgba(245, 138, 18, .2); }
		.password-wrap { position: relative; }
		.password-wrap input { padding-right: 48px; }

		.toggle-password {
			position: absolute;
			top: 50%;
			right: 7px;
			width: 34px;
			height: 34px;
			display: grid;
			place-items: center;
			transform: translateY(-50%);
			border: 0;
			border-radius: 6px;
			background: transparent;
			color: #c7ccce;
			cursor: pointer;
		}

		.toggle-password:hover { background: rgba(255,255,255,.08); color: white; }
		.toggle-password svg { width: 19px; height: 19px; }

		.submit-button {
			width: 100%;
			min-height: 44px;
			margin-top: 21px;
			border: 0;
			border-radius: 8px;
			background: var(--orange);
			box-shadow: 0 7px 18px rgba(245, 138, 18, .22);
			color: white;
			cursor: pointer;
			font: inherit;
			font-weight: 700;
			transition: background .18s ease, transform .18s ease;
		}

		.submit-button:hover { transform: translateY(-1px); background: #ff981e; }
		.submit-button:focus-visible, .toggle-password:focus-visible, .secondary-actions button:focus-visible { outline: 2px solid white; outline-offset: 3px; }
		.secondary-actions { display: grid; justify-items: center; gap: 8px; margin-top: 18px; }
		.secondary-actions button, .secondary-actions a { padding: 2px 4px; border: 0; background: transparent; color: var(--muted); cursor: pointer; font: inherit; font-size: 14px; text-decoration: none; }
		.secondary-actions button:hover, .secondary-actions a:hover { color: white; text-decoration: underline; }
		
		/* Modifiqué el color del mensaje para que los errores se destaquen */
		.form-message { min-height: 18px; margin: 11px 0 0; color: #ff6b6b; text-align: center; font-size: 13px; font-weight: bold; }

		@keyframes enter { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

		@media (max-width: 700px) {
			body { padding: 22px 16px; }
			.backdrop { grid-template-columns: 1fr; padding: 0; opacity: .16; }
			.metrics { display: none; }
			.terminal-panel { position: absolute; inset: 10% -25% auto 20%; min-height: 80vh; transform: rotate(-8deg); }
			.login-card { padding: 30px clamp(22px, 8vw, 42px) 27px; }
		}

		@media (prefers-reduced-motion: reduce) {
			*, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; transition-duration: .01ms !important; }
		}
	</style>
</head>
<body>
	<div class="backdrop" aria-hidden="true">
		<div class="metrics">
			<section class="metric-panel">
				<strong>Resumen de capacitacion</strong>
				<div class="bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
			</section>
			<section class="metric-panel">
				<strong>Estado de aprendizaje</strong>
				<div class="donut"></div>
			</section>
		</div>
		<section class="terminal-panel">
			<div class="terminal-dots"><i></i><i></i><i></i></div>
			<p><span>const</span> seguridad = "ciberconciencia";</p>
			<p><span>function</span> proteger(cuenta) {</p>
			<p>&nbsp;&nbsp;verificarAcceso(cuenta);</p>
			<p>&nbsp;&nbsp;registrarActividad(cuenta);</p>
			<p>&nbsp;&nbsp;return "entorno seguro";</p>
			<p>}</p>
			<p><span>await</span> capacitacion.iniciar();</p>
		</section>
	</div>

	<main class="login-card">
		<header class="brand">
			<svg class="brand-mark" viewBox="0 0 64 64" fill="none" aria-hidden="true">
				<path d="M32 4 54 12v17c0 14-8.7 24.7-22 31C18.7 53.7 10 43 10 29V12L32 4Z" stroke="currentColor" stroke-width="4" />
				<path d="M24 29v-5a8 8 0 0 1 16 0v5m-19 0h22v17H21V29Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round" />
				<circle cx="32" cy="37" r="2.5" fill="currentColor" />
				<path d="M32 39v3" stroke="currentColor" stroke-width="3" />
			</svg>
			<p class="brand-name">DAYTONA</p>
			<p class="brand-subtitle">CIBERCONCIENCIA</p>
		</header>

		<h1>LOGIN DE CAPACITACIÓN</h1>
		<p class="intro">Accedé a tus cursos de concientización sobre ciberseguridad.</p>

		<form method="POST" action="login.php">
			<div class="field">
				<label for="email">Email</label>
				<!-- Ajusté los nombres para que conecten con el backend (name="email") -->
				<input id="email" name="email" type="email" placeholder="Ej. correo@empresa.com" autocomplete="username" required autofocus>
			</div>
			<div class="field">
				<label for="password">Contraseña</label>
				<div class="password-wrap">
					<!-- Ajusté los nombres para que conecten con el backend (name="password") -->
					<input id="password" name="password" type="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
					<button class="toggle-password" type="button" aria-label="Mostrar contraseña" aria-pressed="false" title="Mostrar contraseña">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" />
							<circle cx="12" cy="12" r="3" />
						</svg>
					</button>
				</div>
			</div>
			<button class="submit-button" type="submit">INICIAR SESIÓN</button>
			<!-- Aquí se imprimirá el error de PHP si las credenciales son incorrectas -->
			<p class="form-message" id="form-message" role="status" aria-live="polite">
				<?php if($error != '') echo htmlspecialchars($error); ?>
			</p>
		</form>

		<nav class="secondary-actions" aria-label="Opciones de cuenta">
			<button type="button" data-action="recuperar">Olvidé mi contraseña</button>
			<!-- Cambié el botón por un enlace real a registro.php -->
			<a href="registro.php">Registrar nuevo usuario</a>
		</nav>
	</main>

	<script>
		const passwordInput = document.querySelector("#password");
		const togglePassword = document.querySelector(".toggle-password");
		const message = document.querySelector("#form-message");

		togglePassword.addEventListener("click", () => {
			const willShow = passwordInput.type === "password";
			passwordInput.type = willShow ? "text" : "password";
			togglePassword.setAttribute("aria-pressed", String(willShow));
			togglePassword.setAttribute("aria-label", willShow ? "Ocultar contraseña" : "Mostrar contraseña");
			togglePassword.title = willShow ? "Ocultar contraseña" : "Mostrar contraseña";
		});

		document.querySelectorAll("[data-action]").forEach((button) => {
			button.addEventListener("click", () => {
				if (button.dataset.action === "recuperar") {
					message.textContent = "La recuperación de contraseña se agregará en el siguiente paso.";
					message.style.color = "#ffc57a"; // Vuelve a color naranja para advertencias
				}
			});
		});
	</script>
</body>
</html>
