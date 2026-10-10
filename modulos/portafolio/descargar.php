<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:0;
$q=$pdo->prepare('SELECT m.*,p.estado,p.eliminado_en,p.autor_usuario_id FROM pro_medios m JOIN pro_proyectos p ON p.id=m.proyecto_id WHERE m.id=?');$q->execute([$id]);$m=$q->fetch();
if(!$m||$m['eliminado_en']!==null){http_response_code(404);exit('Archivo no encontrado.');}
$u=usuario_actual();$owner=$u&&(int)$m['autor_usuario_id']===(int)$u['id'];
if($m['estado']!=='publicado'&&!es_curador()&&!$owner){http_response_code(403);exit('No tienes permiso para descargar este archivo.');}
$path=ruta_uploads().'/'.$m['nombre_archivo'];
if(!is_file($path)){http_response_code(404);exit('Archivo no encontrado.');}
header('X-Content-Type-Options: nosniff');header('Content-Type: '.$m['mime']);header('Content-Length: '.filesize($path));
$inline=($m['tipo']==='imagen');header('Content-Disposition: '.($inline?'inline':'attachment').'; filename="proyecto-'.$id.'.'.pathinfo($m['nombre_archivo'],PATHINFO_EXTENSION).'"');
readfile($path);
