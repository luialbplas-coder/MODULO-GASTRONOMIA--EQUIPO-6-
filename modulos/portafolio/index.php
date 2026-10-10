<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$carreras = $pdo->query('SELECT id, nombre FROM carreras WHERE activa=1 ORDER BY nombre')->fetchAll();
$cats = $pdo->query('SELECT id, nombre FROM pro_categorias WHERE activa=1 ORDER BY nombre')->fetchAll();
$types = $pdo->query("SELECT DISTINCT tipo FROM pro_proyectos WHERE estado='publicado' AND eliminado_en IS NULL AND tipo<>'' ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
$where = ["p.estado='publicado'", 'p.eliminado_en IS NULL', 'c.activa=1'];
$params = [];
foreach (['carrera_id'=>'p.carrera_id', 'anio'=>'p.anio', 'categoria_id'=>'p.categoria_id'] as $key=>$col) {
    if (isset($_GET[$key]) && ctype_digit((string)$_GET[$key]) && (int)$_GET[$key] > 0) {
        $where[] = "$col = ?"; $params[] = (int)$_GET[$key];
    }
}
if (!empty($_GET['tipo'])) { $where[] = 'p.tipo = ?'; $params[] = trim((string)$_GET['tipo']); }
if (!empty($_GET['q'])) { $where[] = '(p.titulo LIKE ? OR p.resumen LIKE ?)'; $params[] = '%'.trim((string)$_GET['q']).'%'; $params[] = '%'.trim((string)$_GET['q']).'%'; }
$sql = "SELECT p.id,p.titulo,p.resumen,p.tipo,p.anio,p.destacado,c.nombre carrera,cat.nombre categoria,(SELECT m.id FROM pro_medios m WHERE m.proyecto_id=p.id AND m.tipo='imagen' ORDER BY m.id LIMIT 1) portada FROM pro_proyectos p JOIN carreras c ON c.id=p.carrera_id LEFT JOIN pro_categorias cat ON cat.id=p.categoria_id WHERE ".implode(' AND ',$where)." ORDER BY p.destacado DESC,p.creado_en DESC";
$stmt=$pdo->prepare($sql); $stmt->execute($params); $proyectos=$stmt->fetchAll();
$years=$pdo->query("SELECT DISTINCT anio FROM pro_proyectos WHERE estado='publicado' AND eliminado_en IS NULL ORDER BY anio DESC")->fetchAll(PDO::FETCH_COLUMN);
encabezado('Portafolio de proyectos');
?>
<section class="hero"><p class="eyebrow">Explorador Vocacional · UTBB</p><h1>Ideas que se convierten<br><em>en proyectos.</em></h1><p>Conoce el trabajo de estudiantes de nuestras carreras: propuestas, prácticas e historias hechas en la universidad.</p><a class="boton claro" href="#galeria">Explorar proyectos</a></section>
<section class="intro"><div><p class="eyebrow">Talento universitario</p><h2>Aprender también es crear.</h2></div><p>Explora proyectos de Gastronomía y de otras carreras. Cada ficha reúne su propósito, autores y materiales.</p></section>
<form class="filtros" method="get" id="galeria"><input name="q" value="<?=h($_GET['q']??'')?>" placeholder="Buscar un proyecto"><select name="carrera_id"><option value="">Todas las carreras</option><?php foreach($carreras as $c):?><option value="<?=$c['id']?>" <?=((string)($_GET['carrera_id']??'')===(string)$c['id'])?'selected':''?>><?=h($c['nombre'])?></option><?php endforeach;?></select><select name="anio"><option value="">Todos los años</option><?php foreach($years as $y):?><option value="<?=$y?>" <?=((string)($_GET['anio']??'')===(string)$y)?'selected':''?>><?=$y?></option><?php endforeach;?></select><select name="categoria_id"><option value="">Todas las categorías</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=((string)($_GET['categoria_id']??'')===(string)$c['id'])?'selected':''?>><?=h($c['nombre'])?></option><?php endforeach;?></select><select name="tipo"><option value="">Todos los tipos</option><?php foreach($types as $t):?><option value="<?=h($t)?>" <?=((string)($_GET['tipo']??'')===$t)?'selected':''?>><?=h($t)?></option><?php endforeach;?></select><button class="boton">Filtrar</button></form>
<section class="grid"><?php foreach($proyectos as $p):?><article class="card"><?php if($p['portada']):?><a href="proyecto.php?id=<?=$p['id']?>"><img class="portada" src="descargar.php?id=<?=$p['portada']?>" alt="<?=h($p['titulo'])?>"></a><?php else:?><div class="portada vacia"><span>PRO · <?=$p['anio']?></span></div><?php endif;?><div class="cardbody"><?php if($p['destacado']):?><span class="tag destacado">Destacado</span><?php endif;?><p class="eyebrow"><?=h($p['carrera'])?> · <?=h($p['tipo'])?></p><h3><a href="proyecto.php?id=<?=$p['id']?>"><?=h($p['titulo'])?></a></h3><p><?=h($p['resumen'])?></p><a class="leer" href="proyecto.php?id=<?=$p['id']?>">Ver proyecto →</a></div></article><?php endforeach;?></section>
<?php if(!$proyectos):?><p class="vacio-msg">Todavía no hay proyectos que coincidan con esos filtros.</p><?php endif;?>
<?php if(usuario_actual() && !es_curador()):?><section class="cta"><div><p class="eyebrow">¿Tienes una propuesta?</p><h2>Comparte lo que has creado.</h2></div><a class="boton" href="enviar.php">Enviar proyecto</a></section><?php endif;?>
<?php pie(); ?>
