<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$u=exigir_curador();
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();$action=$_POST['accion']??'';$id=(int)($_POST['id']??0);
 if($action==='crear'){
  $name=trim((string)($_POST['nombre']??''));
  if(mb_strlen($name)>=2&&mb_strlen($name)<=80){try{$q=$pdo->prepare('INSERT INTO pro_categorias(nombre) VALUES(?)');$q->execute([$name]);aviso('ok','Categoría creada.');}catch(PDOException $e){aviso('error','Esa categoría ya existe o no pudo guardarse.');}}
  else aviso('error','Escribe un nombre de 2 a 80 caracteres.');
 }elseif($action==='toggle'&&$id){$q=$pdo->prepare('UPDATE pro_categorias SET activa=IF(activa=1,0,1) WHERE id=?');$q->execute([$id]);aviso('ok','Disponibilidad actualizada.');}
 header('Location: categorias.php');exit;
}
$rows=$pdo->query('SELECT c.*, (SELECT COUNT(*) FROM pro_proyectos p WHERE p.categoria_id=c.id AND p.eliminado_en IS NULL) proyectos FROM pro_categorias c ORDER BY c.nombre')->fetchAll();
encabezado('Categorías');
?><p class="eyebrow">Curaduría</p><h1>Categorías</h1><section class="formwrap"><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="accion" value="crear"><label>Nueva categoría<input name="nombre" required maxlength="80"></label><button class="boton">Agregar categoría</button></form></section><div class="tabla"><table><thead><tr><th>Categoría</th><th>Proyectos</th><th>Estado</th><th></th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=h($r['nombre'])?></td><td><?=$r['proyectos']?></td><td><?=$r['activa']?'Activa':'Oculta'?></td><td><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="accion" value="toggle"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="mini"><?=$r['activa']?'Ocultar':'Activar'?></button></form></td></tr><?php endforeach;?></tbody></table></div><?php pie(); ?>
