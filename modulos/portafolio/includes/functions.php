<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

function carrera_nombre(PDO $pdo, int $id): string {
    $q = $pdo->prepare('SELECT nombre FROM carreras WHERE id = ? AND activa = 1');
    $q->execute([$id]);
    return (string)($q->fetchColumn() ?: 'Carrera');
}
function autores_proyecto(PDO $pdo, int $id): array {
    $q = $pdo->prepare('SELECT nombre, contribucion FROM pro_autores WHERE proyecto_id = ? ORDER BY id');
    $q->execute([$id]);
    return $q->fetchAll();
}
function medios_proyecto(PDO $pdo, int $id): array {
    $q = $pdo->prepare('SELECT id, tipo, nombre_original, mime FROM pro_medios WHERE proyecto_id = ? ORDER BY id');
    $q->execute([$id]);
    return $q->fetchAll();
}
function proyecto_visible(PDO $pdo, int $id): ?array {
    $q = $pdo->prepare("SELECT p.*, c.nombre AS carrera FROM pro_proyectos p JOIN carreras c ON c.id=p.carrera_id WHERE p.id=? AND p.estado='publicado' AND p.eliminado_en IS NULL AND c.activa=1");
    $q->execute([$id]);
    return $q->fetch() ?: null;
}
function ruta_uploads(): string { return dirname(__DIR__) . '/uploads'; }
