<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
if(usuario_actual()){header('Location: index.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();$email=trim((string)($_POST['correo']??''));$password=(string)($_POST['contrasena']??'');
 $q=$pdo->prepare('SELECT id,nombre,correo,contrasena,rol,carrera_id,activo FROM usuarios WHERE correo=? AND activo=1 LIMIT 1');$q->execute([$email]);$u=$q->fetch();
 if($u&&password_verify($password,$u['contrasena'])&&in_array($u['rol'],['aspirante','editor','coordinacion'],true)){
  session_regenerate_id(true);$_SESSION['pro_usuario']=['id'=>(int)$u['id'],'nombre'=>$u['nombre'],'correo'=>$u['correo'],'rol'=>$u['rol'],'carrera_id'=>$u['carrera_id']];header('Location: '.(in_array($u['rol'],['editor','coordinacion'],true)?'index.php':'../enviar.php'));exit;
 }$error='Correo o contraseña incorrectos.';
}
encabezado('Acceso');
?><section class="formwrap estrecho"><p class="eyebrow">Panel de curaduría</p><h1>Acceso</h1><p>Utiliza una cuenta de la plataforma UTBB.</p><?php if($error):?><p class="aviso error"><?=h($error)?></p><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><label>Correo<input type="email" name="correo" required autocomplete="username"></label><label>Contraseña<input type="password" name="contrasena" required autocomplete="current-password"></label><button class="boton">Iniciar sesión</button></form></section><?php pie(); ?>
