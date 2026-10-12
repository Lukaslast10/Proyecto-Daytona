<?php
session_start();
require __DIR__ . '/conexion.php';

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener el nombre del usuario para saludarlo
$stmtUser = $pdo->prepare("SELECT nombre_usuario FROM usuarios WHERE id_usuario = ?");
$stmtUser->execute([$_SESSION['usuario_id']]);
$usuario_actual = $stmtUser->fetch(PDO::FETCH_ASSOC);
$nombre_usuario = $usuario_actual ? explode(' ', $usuario_actual['nombre_usuario'])[0] : 'Usuario';

// Obtener los cursos directamente de la base de datos
$stmt = $pdo->query("SELECT * FROM cursos");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Daytona Ciberconciencia</title>
    <style>
        :root {
            --bg-dark: #16181a;
            --panel-bg: rgba(39, 43, 47, 0.5);
            --panel-border: rgba(255, 255, 255, 0.06);
            --orange: #f58a12;
            --text-main: #f4f5f5;
            --text-muted: #9da6a5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* 1. SCROLLBAR PERSONALIZADO */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(245, 138, 18, 0.5); }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* BARRA LATERAL ESTÁTICA */
        .sidebar {
            width: 260px;
            background: rgba(25, 28, 31, 0.95);
            border-right: 1px solid var(--panel-border);
            display: flex;
            flex-direction: column;
            padding: 35px 25px;
            box-shadow: 5px 0 20px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 50px;
        }
        
        .brand-text { font-size: 20px; font-weight: 800; color: var(--text-main); letter-spacing: 1px;}
        .brand-text span { color: var(--orange); }

        .nav-menu { display: flex; flex-direction: column; gap: 8px; flex-grow: 1; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            font-size: 15px;
            border-left: 3px solid transparent;
        }
        .nav-item.active { 
            background: rgba(245, 138, 18, 0.08); 
            color: var(--orange); 
            border-left-color: var(--orange);
        }
        .nav-item.logout { margin-top: auto; color: #ff6b6b; }
        .nav-item.logout:hover { background: rgba(255, 107, 107, 0.1); border-left-color: #ff6b6b; }

        .nav-item svg { width: 18px; height: 18px; opacity: 0.8; }

        /* CONTENIDO PRINCIPAL */
        .main-content {
            flex-grow: 1;
            padding: 50px 70px;
            background: radial-gradient(circle at top right, rgba(245, 138, 18, 0.04), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(255, 255, 255, 0.02), transparent 40%);
            overflow-y: auto;
        }

        .header-top {
            margin-bottom: 50px;
            border-bottom: 1px solid var(--panel-border);
            padding-bottom: 25px;
        }

        .greeting h1 { font-size: 32px; font-weight: 700; margin-bottom: 10px; }
        .greeting p { color: var(--text-muted); font-size: 16px; }

        /* 2. ANIMACIÓN DE ENTRADA EN CASCADA */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cursos-list {
            display: flex;
            flex-direction: column;
            gap: 25px; 
            max-width: 1000px; 
        }

        .curso-card {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-left: 6px solid var(--orange);
            border-radius: 16px; 
            padding: 35px 45px; 
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            /* Ocultamos las tarjetas por defecto para que las anime el CSS */
            opacity: 0;
            animation: fadeUp 0.6s forwards;
        }

        .curso-card:hover { 
            transform: translateX(10px); 
            border-color: rgba(245, 138, 18, 0.5); 
            background: rgba(39, 43, 47, 0.85);
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        }

        .curso-contenido {
            display: flex;
            align-items: center;
            gap: 35px;
            flex-grow: 1;
            padding-right: 30px;
        }

        .curso-numero {
            font-size: 58px;
            font-weight: 900;
            color: rgba(245, 138, 18, 0.12);
            line-height: 1;
            transition: color 0.3s;
            font-family: "Arial Black", sans-serif;
            letter-spacing: -2px;
        }

        .curso-card:hover .curso-numero { color: rgba(245, 138, 18, 0.5); }

        .curso-info { display: flex; flex-direction: column; gap: 10px; }
        .curso-info h3 { font-size: 21px; margin: 0; color: var(--text-main); letter-spacing: 0.5px; }
        .curso-info p { color: #b7bdc1; font-size: 15.5px; line-height: 1.6; margin: 0; }

        .btn-ingresar {
            white-space: nowrap;
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .curso-card:hover .btn-ingresar { 
            background: var(--orange); 
            color: white; 
            border-color: var(--orange);
            box-shadow: 0 5px 20px rgba(245, 138, 18, 0.3);
        }

        .btn-ingresar::after { content: "→"; font-size: 18px; transition: transform 0.3s; }
        .curso-card:hover .btn-ingresar::after { transform: translateX(5px); }

        @media (max-width: 850px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--panel-border); padding: 20px; flex-direction: row; justify-content: space-between; align-items: center; }
            .nav-menu { display: none; }
            .brand { margin-bottom: 0; }
            .main-content { padding: 30px 20px; }
            .curso-card { flex-direction: column; align-items: flex-start; padding: 30px; gap: 25px;}
            .curso-contenido { flex-direction: column; align-items: flex-start; gap: 15px; padding-right: 0;}
            .curso-numero { font-size: 40px; }
            .btn-ingresar { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-text">DAYTONA<span>.</span></div>
        </div>
        <nav class="nav-menu">
            <a href="#" class="nav-item active">
                <!-- Ícono SVG para Mis Cursos -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Mi Capacitación
            </a>
            <a href="logout.php" class="nav-item logout">
                <!-- Ícono SVG para Cerrar Sesión -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Cerrar Sesión
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="header-top">
            <div class="greeting">
                <h1>Hola, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
                <p>Selecciona un módulo para comenzar tu entrenamiento contra el Phishing.</p>
            </div>
        </div>

        <div class="cursos-list">
            <?php 
            $contador = 1;
            foreach ($cursos as $curso): 
                $numero_formateado = str_pad($contador, 2, "0", STR_PAD_LEFT);
                // Le damos un retraso a la animación basado en el contador para que aparezcan en cascada (0.1s, 0.2s, 0.3s...)
                $retraso_animacion = $contador * 0.15;
            ?>
                <div class="curso-card" style="animation-delay: <?php echo $retraso_animacion; ?>s;">
                    <div class="curso-contenido">
                        <div class="curso-numero"><?php echo $numero_formateado; ?></div>
                        <div class="curso-info">
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                        </div>
                    </div>
                    <a href="ver_curso.php?id=<?php echo $curso['id_curso']; ?>" class="btn-ingresar">
                        Ingresar al Módulo
                    </a>
                </div>
            <?php 
            $contador++;
            endforeach; 
            ?>
        </div>
    </main>

</body>
</html>
