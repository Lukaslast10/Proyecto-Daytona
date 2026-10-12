<?php
session_start();
require __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Error: No se seleccionó ningún curso. <a href='dashboard.php'>Volver al inicio</a>");
}

$id_curso = $_GET['id'];

// Obtener información del curso
$stmtCurso = $pdo->prepare("SELECT * FROM cursos WHERE id_curso = ?");
$stmtCurso->execute([$id_curso]);
$cursoInfo = $stmtCurso->fetch(PDO::FETCH_ASSOC);

if (!$cursoInfo) {
    die("El curso no existe. <a href='dashboard.php'>Volver al inicio</a>");
}

// Obtener todos los módulos del curso
$stmtModulos = $pdo->prepare("SELECT * FROM modulos WHERE id_curso = ?");
$stmtModulos->execute([$id_curso]);
$modulos = $stmtModulos->fetchAll(PDO::FETCH_ASSOC);

// Determinar cuál es el módulo activo (si el usuario hizo clic en uno, o el primero por defecto)
$modulo_activo_id = isset($_GET['modulo']) ? $_GET['modulo'] : (count($modulos) > 0 ? $modulos[0]['id_modulo'] : null);

// Buscar los datos específicos del módulo activo
$modulo_activo = null;
foreach ($modulos as $mod) {
    if ($mod['id_modulo'] == $modulo_activo_id) {
        $modulo_activo = $mod;
        break;
    }
}

// Imágenes simuladas de alta calidad para la cabecera de cada módulo
$imagenes_modulos = [
    1 => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1200&q=80',
    2 => 'https://images.unsplash.com/photo-1614064641936-38998978c9cb?auto=format&fit=crop&w=1200&q=80',
    3 => 'https://images.unsplash.com/photo-1593642532744-d377ab507dc8?auto=format&fit=crop&w=1200&q=80',
    4 => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&w=1200&q=80',
    5 => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80',
    6 => 'https://images.unsplash.com/photo-1508385082359-f38ae991e8f2?auto=format&fit=crop&w=1200&q=80'
];

$img_cabecera = isset($imagenes_modulos[$modulo_activo_id]) ? $imagenes_modulos[$modulo_activo_id] : 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estudio: <?php echo htmlspecialchars($cursoInfo['titulo']); ?> | Daytona</title>
    <style>
        :root {
            --ink: #202326;
            --panel: #292d31;
            --panel-edge: rgba(255, 255, 255, .15);
            --muted: #b7bdc1;
            --orange: #f58a12;
            --orange-light: #ffad42;
            --field: #343a3f;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            color: #f4f5f5;
            background: #1d2022;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            overflow: hidden;
        }

        /* BARRA SUPERIOR (FIJA) */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            height: 65px;
            background: rgba(30, 34, 37, 0.98);
            border-bottom: 1px solid var(--panel-edge);
            flex-shrink: 0;
            z-index: 10;
        }

        .nav-left { display: flex; align-items: center; gap: 20px; }
        .btn-volver { color: var(--muted); text-decoration: none; font-size: 14px; transition: color 0.2s; border-right: 1px solid var(--panel-edge); padding-right: 20px;}
        .btn-volver:hover { color: white; }
        .brand-name { margin: 0; color: var(--orange-light); font-size: 18px; font-weight: 800; letter-spacing: 1px; }

        /* CONTENEDOR TIPO APLICACIÓN */
        .app-container {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
        }

        /* SIDEBAR (ÍNDICE DEL CURSO) */
        .sidebar {
            width: 320px;
            background: var(--panel);
            border-right: 1px solid var(--panel-edge);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid var(--panel-edge);
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-header h3 { margin: 0 0 5px 0; color: white; font-size: 16px; line-height: 1.4; }
        .sidebar-header p { margin: 0; color: var(--orange-light); font-size: 12px; font-weight: bold; text-transform: uppercase;}

        .modulos-lista {
            overflow-y: auto;
            padding: 15px 0;
            flex-grow: 1;
        }

        .modulo-item {
            display: block;
            padding: 15px 20px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14.5px;
            border-left: 4px solid transparent;
            transition: all 0.2s;
            line-height: 1.4;
        }

        .modulo-item:hover { background: rgba(255,255,255,0.03); color: white; }
        
        .modulo-item.activo {
            background: rgba(245, 138, 18, 0.1);
            border-left-color: var(--orange);
            color: white;
            font-weight: bold;
        }

        .modulo-numero { color: var(--orange); margin-right: 8px; font-weight: bold; }

        /* ÁREA DE ESTUDIO (DERECHA) */
        .main-content {
            flex-grow: 1;
            overflow-y: auto;
            background: linear-gradient(115deg, #383d40, #222629 54%, #1d2022);
            position: relative;
        }

        .study-area {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
        }

        /* CABECERA INMERSIVA DEL MÓDULO */
        .modulo-hero {
            width: 100%;
            height: 300px;
            border-radius: 16px;
            background-image: linear-gradient(to top, rgba(29,32,34,1) 0%, rgba(29,32,34,0.3) 50%, rgba(0,0,0,0.1) 100%), url('<?php echo $img_cabecera; ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: flex-end;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .modulo-hero h1 { margin: 0; color: white; font-size: 32px; font-weight: 700; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }

        /* CONTENIDO DE LECTURA */
        .contenido-texto {
            background: rgba(39, 43, 47, 0.8);
            border: 1px solid var(--panel-edge);
            border-radius: 12px;
            padding: 40px;
            font-size: 16.5px;
            line-height: 1.8;
            color: #d8dcdd;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* CAJA DE ACCIÓN FINAL */
        .action-box {
            margin-top: 30px;
            padding: 30px;
            background: rgba(245, 138, 18, 0.05);
            border: 1px dashed var(--orange-light);
            border-radius: 12px;
            text-align: center;
        }

        .action-box h4 { margin: 0 0 15px 0; color: var(--orange-light); font-size: 18px; }
        
        .btn-accion {
            display: inline-block;
            padding: 12px 30px;
            background: var(--orange);
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: transform 0.2s, background 0.2s;
        }
        .btn-accion:hover { background: #ff981e; transform: translateY(-2px); }

        /* Adaptabilidad */
        @media (max-width: 850px) {
            .app-container { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--panel-edge); max-height: 250px; }
            .study-area { padding: 20px; }
            .modulo-hero { height: 200px; padding: 25px; }
            .modulo-hero h1 { font-size: 24px; }
        }
    </style>
</head>
<body>

    <!-- BARRA SUPERIOR -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="dashboard.php" class="btn-volver">← Panel de Cursos</a>
            <p class="brand-name">DAYTONA CIBERCONCIENCIA</p>
        </div>
        <div style="color: var(--muted); font-size: 13px;">Entorno Seguro de Capacitación</div>
    </nav>
    
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="app-container">
        
        <!-- SIDEBAR (ÍNDICE) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <p>Curso actual</p>
                <h3><?php echo htmlspecialchars($cursoInfo['titulo']); ?></h3>
            </div>
            <div class="modulos-lista">
                <?php if (count($modulos) > 0): ?>
                    <?php $contador = 1; foreach ($modulos as $mod): ?>
                        <?php $es_activo = ($mod['id_modulo'] == $modulo_activo_id) ? 'activo' : ''; ?>
                        
                        <a href="ver_curso.php?id=<?php echo $id_curso; ?>&modulo=<?php echo $mod['id_modulo']; ?>" 
                           class="modulo-item <?php echo $es_activo; ?>">
                            <span class="modulo-numero"><?php echo $contador; ?>.</span> 
                            <?php echo htmlspecialchars($mod['titulo_modulo']); ?>
                        </a>
                    <?php $contador++; endforeach; ?>
                <?php else: ?>
                    <p style="padding: 20px; color: var(--muted);">No hay módulos aún.</p>
                <?php endif; ?>
            </div>
        </aside>

        <!-- ÁREA DE ESTUDIO -->
        <main class="main-content">
            <div class="study-area">
                
                <?php if ($modulo_activo): ?>
                    <!-- CABECERA DEL MÓDULO (HERO) -->
                    <div class="modulo-hero">
                        <h1><?php echo htmlspecialchars($modulo_activo['titulo_modulo']); ?></h1>
                    </div>

                    <!-- CONTENIDO DE LECTURA -->
                    <div class="contenido-texto">
                        <?php echo nl2br(htmlspecialchars($modulo_activo['contenido'])); ?>
                    </div>

                    <!-- CAJA DE ACCIÓN (SIMULACIÓN DE INTERACTIVIDAD) -->
                    <div class="action-box">
                        <h4>¿Entendiste los conceptos de este módulo?</h4>
                        <p style="color: var(--muted); margin-bottom: 20px; font-size: 14px;">Pon a prueba lo aprendido identificando un correo de phishing real.</p>
                        <a href="#" class="btn-accion">Iniciar Simulación de Ataque</a>
                    </div>
                    
                <?php else: ?>
                    <div class="contenido-texto" style="text-align: center;">
                        <h2 style="color: var(--orange-light);">Curso sin contenido</h2>
                        <p>Selecciona un módulo del menú lateral o solicita al administrador que cargue el temario.</p>
                    </div>
                <?php endif; ?>

            </div>
        </main>

    </div>

</body>
</html>
