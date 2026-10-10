<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$u=exigir_sesion();
if(($u['rol']??'')!=='aspirante'){http_response_code(403);exit('Solo las cuentas aspirante pueden valorar.');}
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método no permitido.');}
check_csrf();$id=filter_input(INPUT_POST,'proyecto_id',FILTER_VALIDATE_INT)?:0;$score=filter_input(INPUT_POST,'puntuacion',FILTER_VALIDATE_INT)?:0;
if($score<1||$score>5){aviso('error','Selecciona una puntuación del 1 al 5.');header('Location: proyecto.php?id='.$id);exit;}
$q=$pdo->prepare("SELECT id FROM pro_proyectos WHERE id=? AND estado='publicado' AND eliminado_en IS NULL");$q->execute([$id]);
if(!$q->fetchColumn()){http_response_code(404);exit('Proyecto no disponible.');}
try{$q=$pdo->prepare('INSERT INTO pro_valoraciones(proyecto_id,usuario_id,puntuacion) VALUES(?,?,?)');$q->execute([$id,$u['id'],$score]);aviso('ok','Gracias por valorar el proyecto.');}
catch(PDOException $e){aviso('error','Ya registraste tu valoración para este proyecto.');}
header('Location: proyecto.php?id='.$id);exit;
