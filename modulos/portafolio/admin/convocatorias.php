<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$u=exigir_curador();
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();$id=(int)($_POST['id']??0);$action=$_POST['accion']??'';
 $title=trim((string)($_POST['titulo']??''));$desc=trim((string)($_POST['descripcion']??''));$start=(string)($_POST['fecha_inicio']??'');$end=(string)($_POST['fecha_cierre']??'');$state=(string)($_POST['estado']??'borrador');
 if($action==='guardar'&&mb_strlen($title)>=4&&in_array($state,['borrador','abierta','cerrada'],true)&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$start)&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$end)&&$end>=$start){
  if($id){$q=$pdo->prepare('UPDATE pro_convocatorias SET titulo=?,descripcion=?,fecha_inicio=?,fecha_cierre=?,estado=? WHERE id=?');$q->execute([$title,$desc,$start,$end,$state,$id]);}
  else{$q=$pdo->prepare('INSERT INTO pro_convocatorias(titulo,descripcion,fecha_inicio,fecha_cierre,estado) VALUES(?,?,?,?,?)');$q->execute([$title,$desc,$start,$end,$state]);}
  aviso('ok','Convocatoria guardada.');header('Location: convocatorias.php');exit;
 }elseif($action==='baja'&&$id){$q=$pdo->prepare('DELETE FROM pro_convocatorias WHERE id=?');$q->execute([$id]);aviso('ok','Convocatoria eliminada.');header('Location: convocatorias.php');exit;}
 else{$error='Revisa el título, el estado y las fechas.';}
}
$edit=null;if(isset($_GET['editar'])&&ctype_digit((string)$_GET['editar'])){$q=$pdo->prepare('SELECT * FROM pro_convocatorias WHERE id=?');$q->execute([(int)$_GET['editar']]);$edit=$q->fetch();}
$rows=$pdo->query('SELECT * FROM pro_convocatorias ORDER BY fecha_inicio DESC')->fetchAll();
encabezado('Convocatorias');
?><p class="eyebrow">Curaduría</p><h1>Convocatorias</h1><?php if($error??false):?><p class="aviso error"><?=h($error)?></p><?php endif;?><section class="formwrap"><h2><?=$edit?'Editar':'Crear'?> convocatoria</h2><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id" value="<?=h((string)($edit['id']??''))?>"><label>Título<input name="titulo" required minlength="4" maxlength="140" value="<?=h($edit['titulo']??'')?>"></label><label>Descripción<textarea name="descripcion" required><?=h($edit['descripcion']??'')?></textarea></label><div class="dos"><label>Inicio<input type="date" name="fecha_inicio" required value="<?=h($edit['fecha_inicio']??'')?>"></label><label>Cierre<input type="date" name="fecha_cierre" required value="<?=h($edit['fecha_cierre']??'')?>"></label></div><label>Estado<select name="estado"><?php foreach(['borrador','abierta','cerrada'] as $s):?><option <?=($edit['estado']??'borrador')===$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></label><button class="boton">Guardar convocatoria</button></form></section><h2>Registradas</h2><div class="tabla"><table><thead><tr><th>Convocatoria</th><th>Fechas</th><th>Estado</th><th></th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=h($r['titulo'])?></td><td><?=h($r['fecha_inicio'])?> – <?=h($r['fecha_cierre'])?></td><td><?=h($r['estado'])?></td><td><a href="?editar=<?=$r['id']?>">Editar</a><form method="post" class="inline" data-confirm="¿Eliminar esta convocatoria? Los proyectos quedarán sin convocatoria."><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="accion" value="baja"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="mini peligro">Eliminar</button></form></td></tr><?php endforeach;?></tbody></table></div><?php pie(); ?>
