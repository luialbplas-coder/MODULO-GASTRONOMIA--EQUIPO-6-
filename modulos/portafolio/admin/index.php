<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$u=exigir_curador();
$counts=$pdo->query("SELECT estado,COUNT(*) total FROM pro_proyectos WHERE eliminado_en IS NULL GROUP BY estado")->fetchAll(PDO::FETCH_KEY_PAIR);
$pending=$pdo->query("SELECT p.id,p.titulo,p.creado_en,c.nombre carrera FROM pro_proyectos p JOIN carreras c ON c.id=p.carrera_id WHERE p.estado='en_revision' AND p.eliminado_en IS NULL ORDER BY p.creado_en")->fetchAll();
encabezado('Curaduría');
?><section class="intro"><div><p class="eyebrow">Panel de curaduría</p><h1>Hola, <?=h($u['nombre'])?>.</h1><p>Revisa los envíos y administra el contenido publicado.</p></div><a class="boton" href="proyectos.php">Administrar proyectos</a></section><div class="stats"><div><b><?=$counts['en_revision']??0?></b><span>En revisión</span></div><div><b><?=$counts['publicado']??0?></b><span>Publicados</span></div><div><b><?=$counts['rechazado']??0?></b><span>Rechazados</span></div></div><section><div class="sectionhead"><h2>Bandeja de revisión</h2><a class="leer" href="convocatorias.php">Administrar convocatorias →</a></div><?php if(!$pending):?><p>No hay proyectos pendientes.</p><?php else:?><div class="tabla"><table><thead><tr><th>Proyecto</th><th>Carrera</th><th>Recibido</th><th></th></tr></thead><tbody><?php foreach($pending as $p):?><tr><td><?=h($p['titulo'])?></td><td><?=h($p['carrera'])?></td><td><?=h($p['creado_en'])?></td><td><a href="proyectos.php?editar=<?=$p['id']?>">Revisar</a></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></section><?php pie(); ?>
