<?php
declare(strict_types=1);

/*
 * Adaptador del modulo. Si el nucleo ya creo una conexion PDO en $pdo, se reutiliza.
 * Para pruebas independientes, configurar PORTAFOLIO_DB_HOST, PORTAFOLIO_DB_NAME,
 * PORTAFOLIO_DB_USER y PORTAFOLIO_DB_PASS en el entorno de PHP.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params(['httponly' => true, 'secure' => $https, 'samesite' => 'Lax']);
    session_start();
}

if (!isset($GLOBALS['pdo']) || !$GLOBALS['pdo'] instanceof PDO) {
    $host = getenv('PORTAFOLIO_DB_HOST') ?: '127.0.0.1';
    $db = getenv('PORTAFOLIO_DB_NAME') ?: 'explorador_utbb';
    $user = getenv('PORTAFOLIO_DB_USER') ?: 'root';
    $pass = getenv('PORTAFOLIO_DB_PASS') ?: '';
    try {
        $GLOBALS['pdo'] = new PDO(
            "mysql:host={$host};dbname={$db};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::ATTR_EMULATE_PREPARES => false]
        );
    } catch (PDOException $e) {
        http_response_code(503);
        exit('El servicio esta temporalmente fuera de linea. Configura la conexion del nucleo.');
    }
}
$pdo = $GLOBALS['pdo'];

function pro_base(): string {
    $marker = '/modulos/portafolio';
    $script = str_replace('\\\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    $position = strpos($script, $marker);
    return $position === false ? $marker : substr($script, 0, $position) . $marker;
}

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function csrf_token(): string {
    if (empty($_SESSION['pro_csrf'])) $_SESSION['pro_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['pro_csrf'];
}
function check_csrf(): void {
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('La sesion del formulario vencio. Regresa e intenta de nuevo.');
    }
}
function usuario_actual(): ?array {
    return $_SESSION['pro_usuario'] ?? null;
}
function exigir_sesion(): array {
    $u = usuario_actual();
    if (!$u) {
        header('Location: ' . pro_base() . '/admin/login.php');
        exit;
    }
    return $u;
}
function exigir_curador(): array {
    $u = exigir_sesion();
    if (!in_array($u['rol'] ?? '', ['editor', 'coordinacion'], true)) {
        http_response_code(403);
        exit('No tienes permisos para acceder a esta seccion.');
    }
    return $u;
}
function es_curador(): bool {
    $u = usuario_actual();
    return $u && in_array($u['rol'] ?? '', ['editor', 'coordinacion'], true);
}
function aviso(string $tipo, string $texto): void { $_SESSION['pro_aviso'] = [$tipo, $texto]; }
function mostrar_aviso(): void {
    if (!empty($_SESSION['pro_aviso'])) {
        [$tipo, $texto] = $_SESSION['pro_aviso']; unset($_SESSION['pro_aviso']);
        echo '<p class="aviso '.h($tipo).'">'.h($texto).'</p>';
    }
}
function encabezado(string $titulo): void {
    $u = usuario_actual();
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.h($titulo).' | Explorador UTBB</title><link rel="stylesheet" href="'.h(pro_base()).'/css/estilos.css"><script src="'.h(pro_base()).'/js/app.js" defer></script></head><body>';
    echo '<header class="top"><a class="marca" href="'.h(pro_base()).'/">Explorador <b>UTBB</b></a><nav><a href="'.h(pro_base()).'/">Proyectos</a>';
    if ($u && es_curador()) echo '<a href="'.h(pro_base()).'/admin/">Curaduria</a>';
    if ($u) echo '<a href="/modulos/portafolio/admin/logout.php">Salir</a>'; else echo '<a href="/modulos/portafolio/admin/login.php">Acceso</a>';
    echo '</nav></header><main class="contenedor">';
    mostrar_aviso();
}
function pie(): void { echo '</main><footer>Explorador Vocacional UTBB · Módulo PRO</footer></body></html>'; }
