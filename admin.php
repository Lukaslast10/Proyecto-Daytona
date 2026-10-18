<?php
session_start();
require __DIR__ . '/conexion.php';

// 1. CONTROL DE ACCESO ESTRICTO
// Verificamos si existe la sesión Y si el usuario tiene el rol 1 (Administrador)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    // Si no es admin o no está logueado, lo redirigimos al login inmediatamente
    header("Location: login.php");
    exit;
}

// 2. OBTENER LOS LOGS DE AUDITORÍA
$logs = [];$db_error = '';

try {
    // Hacemos un JOIN para traer el email del usuario basado en su id_usuario.
    // IMPORTANTE: Asegúrate de que las columnas 'fecha_acceso' e 'id_acceso' coincidan 
    // con los nombres que le pusiste a tu tabla `accesos` en tu base de datos.
    $query = "
        SELECT 
            a.ip_usuario, 
            a.resultado, 
            a.fecha_acceso,
            u.email AS usuario_email
        FROM accesos a
        LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
        ORDER BY a.id_acceso DESC
    ";
    
    $stmt = $pdo->query($query);
    $logs =$stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si falla (por ejemplo, si los nombres de las columnas no coinciden), capturamos el error
    $db_error = "Error en BD: Verifica que las columnas id_acceso y fecha_acceso existan en tu tabla accesos.";
}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#202326">
	<title>Administración | Daytona Ciberconciencia</title>
	<style>
		/* (Tus estilos originales intactos) */
		:root {
			color-scheme: dark;
			--ink: #202326;
			--panel: #292d31;
			--panel-edge: rgba(255, 255, 255, .16);
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
			color: #f4f5f5;
			background:
				radial-gradient(ellipse at 50% 35%, rgba(245, 138, 18, .1), transparent 38%),
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

		.page-shell { width: min(1120px, calc(100% - 48px)); margin: 0 auto; }

		.topbar {
			min-height: 88px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 24px;
			border-bottom: 1px solid rgba(255, 255, 255, .12);
		}

		.brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
		.brand-mark { width: 42px; height: 42px; flex: 0 0 auto; color: var(--orange); filter: drop-shadow(0 0 10px rgba(245, 138, 18, .25)); }
		.brand-name { margin: 0; color: var(--orange-light); font-size: 20px; font-weight: 800; line-height: 1; }
		.brand-subtitle { margin: 4px 0 0; color: #f4a235; font-size: 10px; font-weight: 700; letter-spacing: 1.5px; }

		.top-actions { display: flex; align-items: center; gap: 24px; }
		.top-actions a { color: var(--muted); font-size: 14px; text-decoration: none; transition: color .18s ease; }
		.top-actions a:hover { color: #fff; }
		.top-actions .logout { padding: 9px 13px; border: 1px solid rgba(245, 138, 18, .55); border-radius: 7px; color: var(--orange-light); }
		.top-actions .logout:hover { background: rgba(245, 138, 18, .1); }

		main { padding: 54px 0 72px; }
		.page-heading { margin-bottom: 28px; }
		.eyebrow { margin: 0 0 9px; color: var(--orange-light); font-size: 12px; font-weight: 700; letter-spacing: 1.8px; text-transform: uppercase; }
		h1 { margin: 0; font-size: clamp(26px, 4vw, 34px); font-weight: 600; }
		.page-description { margin: 9px 0 0; color: var(--muted); font-size: 15px; line-height: 1.5; }

		.logs-panel {
			overflow: hidden;
			border: 1px solid var(--panel-edge);
			border-radius: 12px;
			background: rgba(39, 43, 47, .94);
			box-shadow: 0 20px 56px rgba(0, 0, 0, .28);
		}

		.logs-heading { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 21px 24px; border-bottom: 1px solid rgba(255, 255, 255, .1); }
		.logs-heading h2 { margin: 0; font-size: 17px; font-weight: 600; }
		.logs-heading p { margin: 5px 0 0; color: var(--muted); font-size: 13px; }
		.section-mark { width: 34px; height: 34px; display: grid; place-items: center; border: 1px solid rgba(245, 138, 18, .34); border-radius: 8px; color: var(--orange-light); }
		.section-mark svg { width: 18px; height: 18px; }

		.table-scroll { overflow-x: auto; }
		table { width: 100%; border-collapse: collapse; text-align: left; }
		th { padding: 14px 24px; background: rgba(255, 255, 255, .025); color: #aeb5b8; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; white-space: nowrap; }
		td { padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, .08); color: #e4e7e8; font-size: 14px; }
		.empty-state { height: 150px; color: var(--muted); text-align: center; }

		.badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
		.badge-exito { background: rgba(46, 204, 113, 0.15); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3); }
		.badge-fallido { background: rgba(231, 76, 60, 0.15); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3); }

		@media (max-width: 640px) {
			.page-shell { width: min(100% - 32px, 1120px); }
			.topbar { min-height: 76px; }
			.brand-mark { width: 36px; height: 36px; }
			.brand-name { font-size: 17px; }
			.top-actions { gap: 12px; }
			.top-actions a { font-size: 12px; }
			.top-actions .logout { padding: 8px 10px; }
			main { padding-top: 38px; }
			.logs-heading { padding: 18px; }
			table { min-width: 620px; }
			th, td { padding-right: 16px; padding-left: 16px; }
		}
	</style>
</head>
<body>
	<div class="page-shell">
		<header class="topbar">
			<a class="brand" href="dashboard.php" aria-label="Daytona Ciberconciencia, volver a cursos">
				<svg class="brand-mark" viewBox="0 0 64 64" fill="none" aria-hidden="true">
					<path d="M32 4 54 12v17c0 14-8.7 24.7-22 31C18.7 53.7 10 43 10 29V12L32 4Z" stroke="currentColor" stroke-width="4" />
					<path d="M24 29v-5a8 8 0 0 1 16 0v5m-19 0h22v17H21V29Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round" />
					<circle cx="32" cy="37" r="2.5" fill="currentColor" />
					<path d="M32 39v3" stroke="currentColor" stroke-width="3" />
				</svg>
				<span>
					<span class="brand-name">DAYTONA</span>
					<span class="brand-subtitle">CIBERCONCIENCIA</span>
				</span>
			</a>
			<nav class="top-actions" aria-label="Navegación">
				<!-- Removido link de Cursos porque Admin no necesariamente toma cursos, o puedes dejarlo si lo deseas -->
				<span style="color: var(--orange-light); font-size: 14px; font-weight: bold;">Panel Admin</span>
				<a class="logout" href="logout.php">Cerrar sesión</a>
			</nav>
		</header>

		<main>
			<section class="page-heading" aria-labelledby="page-title">
				<p class="eyebrow">Administración</p>
				<h1 id="page-title">Registro de accesos</h1>
				<p class="page-description">Historial de actividad y auditoría de la plataforma.</p>
			</section>

			<section class="logs-panel" aria-labelledby="logs-title">
				<div class="logs-heading">
					<div>
						<h2 id="logs-title">Auditoría</h2>
						<p>Accesos recientes de los usuarios</p>
					</div>
					<span class="section-mark" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M8 6h13M8 12h13M8 18h13" />
							<path d="M3 6h.01M3 12h.01M3 18h.01" />
						</svg>
					</span>
				</div>
				<div class="table-scroll">
					<table>
						<thead>
							<tr>
								<th scope="col">Fecha y hora</th>
								<th scope="col">Usuario (Email)</th>
								<th scope="col">Dirección IP</th>
								<th scope="col">Resultado</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($logs)): ?>
								<?php foreach ($logs as$log): ?>
									<tr>
										<td><?php echo htmlspecialchars($log['fecha_acceso'] ?? 'No registrada'); ?></td>
										<td><?php echo htmlspecialchars($log['usuario_email'] ?? 'Desconocido'); ?></td>
										<td><?php echo htmlspecialchars($log['ip_usuario']); ?></td>
										<td>
											<?php if ($log['resultado'] === 'exito'): ?>
												<span class="badge badge-exito">Éxito</span>
											<?php else: ?>
												<span class="badge badge-fallido">Fallido</span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td class="empty-state" colspan="4">
										<?php echo $db_error != '' ? htmlspecialchars($db_error) : 'No hay registros de acceso disponibles.'; ?>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</section>
		</main>
	</div>
</body>
</html>
