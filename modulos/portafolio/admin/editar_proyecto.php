<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
$u=exigir_curador();
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:0;
$q=$pdo->prepare('SELECT * FROM pro_proyectos WHERE id=? AND eliminado_en IS NULL');$q->execute([$id]);$p=$q->fetch();
if(!$p){http_response_code(404);exit('Proyecto no encontrado.');}
$carreras=$pdo->query('SELECT id,nombre FROM carreras WHERE activa=1 ORDER BY nombre')->fetchAll();
$cats=$pdo->query('SELECT id,nombre FROM pro_categorias WHERE activa=1 ORDER BY nombre')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();$titulo=trim((string)($_POST['titulo']??''));$resumen=trim((string)($_POST['resumen']??''));$descripcion=trim((string)($_POST['descripcion']??''));$tipo=trim((string)($_POST['tipo']??''));$carrera=(int)($_POST['carrera_id']??0);$categoria=(int)($_POST['categoria_id']??0);
 if(mb_strlen($titulo)>=4&&mb_strlen($titulo)<=160&&mb_strlen($resumen)>=10&&mb_strlen($resumen)<=280&&mb_strlen($descripcion)>=20&&in_array($carrera,array_column($carreras,'id'))&&(!$categoria||in_array($categoria,array_column($cats,'id')))&&mb_strlen($tipo)<=80){
  $q=$pdo->prepare('UPDATE pro_proyectos SET titulo=?,resumen=?,descripcion=?,tipo=?,carrera_id=?,categoria_id=?,actualizado_en=CURRENT_TIMESTAMP WHERE id=?');
  $q->execute([$titulo,$resumen,$descripcion,$tipo?:'Proyecto',$carrera,$categoria?:null,$id]);aviso('ok','Proyecto actualizado.');header('Location: proyectos.php');exit;
 }$error='Revisa el título, el resumen, la descripción y la carrera.';
}
encabezado('Editar proyecto');
?><a class="volver" href="proyectos.php">← Volver a proyectos</a><section class="formwrap"><p class="eyebrow">Curaduría</p><h1>Editar proyecto</h1><?php if($error??false):?><p class="aviso error"><?=h($error)?></p><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><label>Título<input name="titulo" required maxlength="160" value="<?=h($p['titulo'])?>"></label><label>Resumen<textarea name="resumen" required maxlength="280"><?=h($p['resumen'])?></textarea></label><label>Descripción<textarea name="descripcion" required><?=h($p['descripcion'])?></textarea></label><div class="dos"><label>Carrera<select name="carrera_id" required><?php foreach($carreras as $c):?><option value="<?=$c['id']?>" <?=((int)$p['carrera_id']===(int)$c['id']?'selected':'')?>><?=h($c['nombre'])?></option><?php endforeach;?></select></label><label>Categoría<select name="categoria_id"><option value="">Sin categoría</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=((int)$p['categoria_id']===(int)$c['id']?'selected':'')?>><?=h($c['nombre'])?></option><?php endforeach;?></select></label></div><label>Tipo<input name="tipo" maxlength="80" value="<?=h($p['tipo'])?>"></label><button class="boton">Guardar cambios</button></form></section><?php pie(); ?>
