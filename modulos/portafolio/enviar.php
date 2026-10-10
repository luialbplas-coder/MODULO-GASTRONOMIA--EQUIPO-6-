<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$u=exigir_sesion();
if(in_array($u['rol'],['editor','coordinacion'],true)){header('Location: admin/');exit;}
$conv=$pdo->query("SELECT id,titulo,fecha_cierre FROM pro_convocatorias WHERE estado='abierta' AND CURRENT_DATE BETWEEN fecha_inicio AND fecha_cierre ORDER BY fecha_cierre LIMIT 1")->fetch();
$carreras=$pdo->query('SELECT id,nombre FROM carreras WHERE activa=1 ORDER BY nombre')->fetchAll();
$cats=$pdo->query('SELECT id,nombre FROM pro_categorias WHERE activa=1 ORDER BY nombre')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();
 if(!$conv){aviso('error','No hay una convocatoria abierta.');header('Location: index.php');exit;}
 $titulo=trim((string)($_POST['titulo']??''));$resumen=trim((string)($_POST['resumen']??''));$descripcion=trim((string)($_POST['descripcion']??''));$tipo=trim((string)($_POST['tipo']??''));$carrera=(int)($_POST['carrera_id']??0);$categoria=(int)($_POST['categoria_id']??0);$autor=trim((string)($_POST['autor']??($u['nombre']??'')));
 $errors=[];
 if(mb_strlen($titulo)<4||mb_strlen($titulo)>160)$errors[]='El título debe tener de 4 a 160 caracteres.';
 if(mb_strlen($resumen)<10||mb_strlen($resumen)>280)$errors[]='El resumen debe tener de 10 a 280 caracteres.';
 if(mb_strlen($descripcion)<20)$errors[]='Agrega una descripción de al menos 20 caracteres.';
 if(!$carrera||!in_array($carrera,array_column($carreras,'id')))$errors[]='Selecciona una carrera válida.';
 if(!$autor||mb_strlen($autor)>140)$errors[]='Escribe el nombre del autor.';
 if(!$errors){
  try{
   $pdo->beginTransaction();
   $s=$pdo->prepare("INSERT INTO pro_proyectos(convocatoria_id,carrera_id,categoria_id,autor_usuario_id,titulo,resumen,descripcion,tipo,anio,estado) VALUES(?,?,?,?,?,?,?,?,YEAR(CURRENT_DATE),'en_revision')");
   $s->execute([$conv['id'],$carrera,$categoria?:null,$u['id'],$titulo,$resumen,$descripcion,$tipo?:'Proyecto']);
   $pid=(int)$pdo->lastInsertId();
   $s=$pdo->prepare('INSERT INTO pro_autores(proyecto_id,usuario_id,nombre,contribucion) VALUES(?,?,?,?)');$s->execute([$pid,$u['id'],$autor,'Autor principal']);
   if(!empty($_FILES['archivo']['name'])) {
    $f=$_FILES['archivo'];
    if($f['error']!==UPLOAD_ERR_OK)throw new RuntimeException('No se pudo recibir el archivo.');
    if($f['size']>20*1024*1024)throw new RuntimeException('El archivo supera el límite de 20 MB.');
    $fi=new finfo(FILEINFO_MIME_TYPE);$mime=$fi->file($f['tmp_name']);
    $allowed=['image/jpeg'=>'imagen','image/png'=>'imagen','image/webp'=>'imagen','video/mp4'=>'video','application/pdf'=>'pdf'];
    if(!isset($allowed[$mime]))throw new RuntimeException('Tipo no permitido. Usa JPG, PNG, WebP, MP4 o PDF.');
    $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','video/mp4'=>'mp4','application/pdf'=>'pdf'][$mime];
    $filename=bin2hex(random_bytes(24)).'.'.$ext;$dir=ruta_uploads();
    if(!is_dir($dir)&&!mkdir($dir,0750,true))throw new RuntimeException('No se pudo preparar almacenamiento.');
    if(!move_uploaded_file($f['tmp_name'],$dir.'/'.$filename))throw new RuntimeException('No se pudo guardar el archivo.');
    $s=$pdo->prepare('INSERT INTO pro_medios(proyecto_id,subido_por,tipo,nombre_original,nombre_archivo,mime,tamano) VALUES(?,?,?,?,?,?,?)');
    $s->execute([$pid,$u['id'],$allowed[$mime],basename((string)$f['name']),$filename,$mime,$f['size']]);
   }
   $pdo->commit();aviso('ok','Envío recibido. El equipo curador lo revisará.');header('Location: index.php');exit;
  }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();$errors[]=$e instanceof RuntimeException?$e->getMessage():'No se pudo guardar el proyecto. Revisa los datos e inténtalo de nuevo.';}
 }
}
encabezado('Enviar proyecto');
?><section class="formwrap"><p class="eyebrow">Convocatoria</p><h1>Comparte tu proyecto</h1><?php if(!$conv):?><p>No hay convocatoria abierta por el momento.</p><?php else:?><p><?=h($conv['titulo'])?> · cierra el <?=h($conv['fecha_cierre'])?></p><?php endif;?><?php foreach($errors??[] as $e):?><p class="aviso error"><?=h($e)?></p><?php endforeach;?><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><label>Título<input name="titulo" required maxlength="160" value="<?=h($_POST['titulo']??'')?>"></label><label>Resumen<textarea name="resumen" required maxlength="280"><?=h($_POST['resumen']??'')?></textarea></label><label>Descripción<textarea name="descripcion" required><?=h($_POST['descripcion']??'')?></textarea></label><div class="dos"><label>Carrera<select name="carrera_id" required><option value="">Selecciona</option><?php foreach($carreras as $c):?><option value="<?=$c['id']?>"><?=h($c['nombre'])?></option><?php endforeach;?></select></label><label>Categoría<select name="categoria_id"><option value="">Otra / sin categoría</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>"><?=h($c['nombre'])?></option><?php endforeach;?></select></label></div><label>Tipo de proyecto<input name="tipo" maxlength="80" placeholder="Ej. Proyecto integrador"></label><label>Nombre del autor principal<input name="autor" required maxlength="140" value="<?=h($u['nombre']??'')?>"></label><label>Archivo opcional <small>JPG, PNG, WebP, MP4 o PDF · máximo 20 MB</small><input type="file" name="archivo" accept=".jpg,.jpeg,.png,.webp,.mp4,.pdf"></label><button class="boton" <?=!$conv?'disabled':''?>>Enviar a revisión</button></form></section><?php pie(); ?>
