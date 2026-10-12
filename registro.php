<?php
session_start();
require 'conexion.php';
$mensaje = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Verificar si el email ya existe en la base de datos
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        $error = "El correo electrónico ya está registrado.";
    } else {
        // Encriptar la contraseña por seguridad
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar el nuevo usuario (id_rol = 2 por defecto para usuarios normales)
        $insert = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, clave, id_rol) VALUES (?, ?, ?, 2)");
        
        if ($insert->execute([$nombre, $email, $hashed_password])) {
            $mensaje = "Registro exitoso. Serás redirigido al login...";
            // Redirigir al login después de 2 segundos para que el usuario lea el mensaje
            header("refresh:2;url=login.php"); 
        } else {
            $error = "Hubo un error al crear la cuenta. Intenta de nuevo.";
        }
    }
}
?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registro | Daytona Ciberconciencia</title>
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
			padding: 32px 20px;
			color: #f4f5f5;
			background:
				radial-gradient(ellipse at 50% 48%, rgba(245, 138, 18, .1), transparent 34%),
				linear-gradient(115deg, #383d40, #222629 54%, #1d2022);
			font-family: "Trebuchet MS", "Segoe UI", sans-serif;
		}

		/* Fondo de cuadrícula sutil */
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

		.login-card {
			position: relative;
			width: min(100%, 464px);
			padding: 34px 56px 32px;
			border: 1px solid var(--panel-edge);
			border-radius: 18px;
			background: rgba(39, 43, 47, .97);
			box-shadow: 0 24px 70px rgba(0, 0, 0, .42), 0 0 32px rgba(245, 138, 18, .1);
			animation: enter .5s ease-out both;
		}

		.brand { margin: 0 auto 22px; text-align: center; }
		.brand-mark { width: 50px; height: 50px; margin: 0 auto 8px; color: var(--orange); filter: drop-shadow(0 0 10px rgba(245, 138, 18, .25)); }
		.brand-name { margin: 0; color: var(--orange-light); font-size: 24px; font-weight: 800; line-height: 1; letter-spacing: .7px; }

		h1 { margin: 15px 0 5px; text-align: center; font-size: 21px; font-weight: 500; }
		.intro { margin: 0 auto 25px; color: #d8dcdd; text-align: center; font-size: 14.5px; line-height: 1.45; }
		
		.field { margin-top: 15px; }
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
			margin-top: 25px;
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
		
		.secondary-actions { display: grid; justify-items: center; gap: 8px; margin-top: 20px; }
		.secondary-actions a { padding: 2px 4px; color: var(--muted); font-size: 14px; text-decoration: none; transition: 0.2s; }
		.secondary-actions a:hover { color: white; text-decoration: underline; }
		
		/* Estilos para mensajes de error o éxito de PHP */
		.msg-error { color: #ff6b6b; text-align: center; font-size: 13.5px; font-weight: bold; margin-bottom: 15px; background: rgba(255, 107, 107, 0.1); padding: 10px; border-radius: 6px; border: 1px solid rgba(255, 107, 107, 0.3);}
		.msg-exito { color: #2ecc71; text-align: center; font-size: 13.5px; font-weight: bold; margin-bottom: 15px; background: rgba(46, 204, 113, 0.1); padding: 10px; border-radius: 6px; border: 1px solid rgba(46, 204, 113, 0.3);}

		@keyframes enter { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

		@media (max-width: 700px) {
			.login-card { padding: 30px clamp(22px, 8vw, 42px) 27px; }
		}
	</style>
</head>
<body>

	<main class="login-card">
		<header class="brand">
			<svg class="brand-mark" viewBox="0 0 64 64" fill="none" aria-hidden="true">
				<path d="M32 4 54 12v17c0 14-8.7 24.7-22 31C18.7 53.7 10 43 10 29V12L32 4Z" stroke="currentColor" stroke-width="4" />
				<path d="M24 29v-5a8 8 0 0 1 16 0v5m-19 0h22v17H21V29Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round" />
				<circle cx="32" cy="37" r="2.5" fill="currentColor" />
				<path d="M32 39v3" stroke="currentColor" stroke-width="3" />
			</svg>
			<p class="brand-name">DAYTONA</p>
		</header>

		<h1>CREAR CUENTA</h1>
		<p class="intro">Regístrate para acceder al panel de capacitación corporativa.</p>

        <!-- Aquí imprimimos los mensajes del Backend PHP -->
        <?php if($error != '') echo "<div class='msg-error'>" . htmlspecialchars($error) . "</div>"; ?>
        <?php if($mensaje != '') echo "<div class='msg-exito'>" . htmlspecialchars($mensaje) . "</div>"; ?>

		<form method="POST" action="registro.php">
            <div class="field">
				<label for="nombre">Nombre Completo</label>
				<input id="nombre" name="nombre" type="text" placeholder="Ej. Juan Pérez" required autofocus>
			</div>

			<div class="field">
				<label for="email">Correo Electrónico (Email)</label>
				<input id="email" name="email" type="email" placeholder="Ej. juan@empresa.com" autocomplete="username" required>
			</div>

			<div class="field">
				<label for="password">Contraseña</label>
				<div class="password-wrap">
					<input id="password" name="password" type="password" placeholder="Crea una contraseña segura" autocomplete="new-password" required minlength="6">
					<button class="toggle-password" type="button" aria-label="Mostrar contraseña" aria-pressed="false" title="Mostrar contraseña">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" />
							<circle cx="12" cy="12" r="3" />
						</svg>
					</button>
				</div>
			</div>

			<button class="submit-button" type="submit">REGISTRARSE</button>
		</form>

		<nav class="secondary-actions" aria-label="Opciones de cuenta">
			<a href="login.php">¿Ya tienes una cuenta? Iniciar Sesión</a>
		</nav>
	</main>

	<script>
		// Script para mostrar/ocultar la contraseña
		const passwordInput = document.querySelector("#password");
		const togglePassword = document.querySelector(".toggle-password");

		togglePassword.addEventListener("click", () => {
			const willShow = passwordInput.type === "password";
			passwordInput.type = willShow ? "text" : "password";
			togglePassword.setAttribute("aria-pressed", String(willShow));
			togglePassword.title = willShow ? "Ocultar contraseña" : "Mostrar contraseña";
		});
	</script>
</body>
</html>
