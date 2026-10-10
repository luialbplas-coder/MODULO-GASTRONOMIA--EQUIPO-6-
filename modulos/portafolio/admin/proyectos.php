<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
$u=exigir_curador();
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();$id=(int)($_POST['id']??0);$action=(string)($_POST['accion']??'');
 if($action==='estado'&&$id){
  $state=(string)($_POST['estado']??'');if(in_array($state,['en_revision','publicado','rechazado'],true)){
   $q=$pdo->prepare('UPDATE pro_proyectos SET estado=?, actualizado_en=CURRENT_TIMESTAMP WHERE id=? AND eliminado_en IS NULL');$q->execute([$state,$id]);
   if($state!=='publicado'){$q=$pdo->prepare('UPDATE pro_proyectos SET destacado=0 WHERE id=?');$q->execute([$id]);}
   aviso('ok','Estado actualizado.');
  }
 }elseif($action==='destacado'&&$id){
  $q=$pdo->prepare("UPDATE pro_proyectos SET destacado=IF(destacado=1,0,1) WHERE id=? AND estado='publicado' AND eliminado_en IS NULL");$q->execute([$id]);aviso('ok','Selección de destacado actualizada.');
 }elseif($action==='baja'&&$id){
  $q=$pdo->prepare('UPDATE pro_proyectos SET eliminado_en=NOW(),destacado=0 WHERE id=?');$q->execute([$id]);aviso('ok','Proyecto dado de baja.');
 }
 header('Location: proyectos.php');exit;
}
$filter=(string)($_GET['estado']??'');$where='p.eliminado_en IS NULL';$params=[];
if(in_array($filter,['en_revision','publicado','rechazado'],true)){$where.=' AND p.estado=?';$params[]=$filter;}
$q=$pdo->prepare("SELECT p.*,c.nombre carrera FROM pro_proyectos p JOIN carreras c ON c.id=p.carrera_id WHERE $where ORDER BY FIELD(p.estado,'en_revision','publicado','rechazado'),p.creado_en DESC");$q->execute($params);$rows=$q->fetchAll();
encabezado('Administrar proyectos');
?><p class="eyebrow">Curaduría</p><h1>Proyectos</h1><form class="filtros compacto"><select name="estado"><option value="">Todos</option><?php foreach(['en_revision'=>'En revisión','publicado'=>'Publicados','rechazado'=>'Rechazados'] as $k=>$v):?><option value="<?=$k?>" <?=($filter===$k?'selected':'')?>><?=$v?></option><?php endforeach;?></select><button class="boton">Filtrar</button></form><div class="tabla"><table><thead><tr><th>Proyecto</th><th>Carrera</th><th>Estado</th><th>Acciones</th></tr></thead><tbody><?php foreach($rows as $p):?><tr><td><a href="../proyecto.php?id=<?=$p['id']?>"><?=h($p['titulo'])?></a><small><?=h($p['tipo'])?> · <?=$p['anio']?></small></td><td><?=h($p['carrera'])?></td><td><?=h(str_replace('_',' ',$p['estado']))?><?=$p['destacado']?' · destacado':''?></td><td class="acciones"><?php foreach(['publicado'=>'Publicar','rechazado'=>'Rechazar','en_revision'=>'Reabrir'] as $s=>$label):?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="id" value="<?=$p['id']?>"><input type="hidden" name="estado" value="<?=$s?>"><input type="hidden" name="accion" value="estado"><button class="mini"><?=$label?></button></form><?php endforeach;?><?php if($p['estado']==='publicado'):?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="id" value="<?=$p['id']?>"><input type="hidden" name="accion" value="destacado"><button class="mini"><?= $p['destacado']?'Quitar destacado':'Destacar'?></button></form><?php endif;?><form method="post" data-confirm="¿Dar de baja este proyecto?"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="id" value="<?=$p['id']?>"><input type="hidden" name="accion" value="baja"><button class="mini peligro">Dar de baja</button></form></td></tr><?php endforeach;?></tbody></table></div><?php if(!$rows):?><p>No hay proyectos con ese filtro.</p><?php endif;?><?php pie(); ?>
