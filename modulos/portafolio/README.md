# Módulo PRO · Portafolio de proyectos

Equipo A-6 · Carrera representada: Gastronomía · Prefijo de tablas: `pro_`

Módulo para el Explorador Vocacional UTBB. Publica trabajos estudiantiles de todas las carreras y permite que aspirantes envíen propuestas a convocatorias. El equipo curador revisa cada envío y decide si se publica, rechaza o destaca.

## Estructura

```text
portafolio/
├── index.php                 Galería pública y filtros
├── proyecto.php              Ficha pública y valoración
├── enviar.php                Formulario de envío para aspirantes
├── valorar.php               Una valoración por usuario/proyecto
├── descargar.php             Descarga/visualización con permiso
├── admin/                    Acceso, bandeja y administración
├── includes/                 Conexión adaptadora y funciones propias
├── css/estilos.css
├── js/app.js
├── uploads/                  Archivos no ejecutables
└── esquema.sql               Tablas y categorías iniciales
```

## Instalación local con XAMPP

1. Copia la carpeta en el repositorio dentro de `modulos/portafolio/`.
2. En XAMPP inicia Apache y MySQL.
3. Primero importa el esquema del núcleo, que debe incluir `usuarios` y `carreras`. No crees ni cambies esas tablas con este módulo.
4. Importa `esquema.sql` en la base de datos común.
5. Para prueba independiente configura en Apache/PHP las variables `PORTAFOLIO_DB_HOST`, `PORTAFOLIO_DB_NAME`, `PORTAFOLIO_DB_USER` y `PORTAFOLIO_DB_PASS`. Si el núcleo ya inicializa una conexión PDO en `$pdo`, el módulo la reutiliza.
6. Da permiso de escritura al servidor web en `uploads/`. No subas archivos cargados al repositorio.
7. Abre `http://localhost/<carpeta-del-repositorio>/modulos/portafolio/`.

El adaptador de sesión usa `$_SESSION['pro_usuario']` y la tabla del núcleo `usuarios` con los campos descritos en la especificación. Al integrar el núcleo, el equipo debe conectar su sesión/autenticación oficial al adaptador de `includes/bootstrap.php`; no debe duplicar usuarios ni modificar el núcleo.

## Roles

- `aspirante`: envía proyectos en convocatorias abiertas y valora proyectos publicados una vez.
- `editor` y `coordinacion`: acceso al panel de curaduría, administración de proyectos y convocatorias.

El documento del módulo llama a estos roles “autor” y “curador”, mientras el documento general establece los roles del núcleo. Aquí se usa aspirante como autor y editor/coordinación como curaduría, sujeto a confirmación del docente.

## Estados y reglas

- Convocatoria: `borrador`, `abierta`, `cerrada`; solo acepta envíos entre sus fechas.
- Proyecto: `en_revision`, `publicado`, `rechazado`. “Destacado” es una marca adicional y solo aplica a publicados.
- Solo se muestran públicamente proyectos publicados, no dados de baja y de carreras activas.
- Las valoraciones requieren sesión; la clave única en la base evita repetir voto por usuario/proyecto.
- Tipos permitidos: JPG, PNG, WebP, MP4 y PDF, hasta 20 MB. El MIME se comprueba con `finfo` y el nombre almacenado se genera en el servidor.

## Seguridad incorporada

PDO con preparaciones reales; validación en servidor; salida HTML escapada con `htmlspecialchars`; tokens CSRF en formularios; comprobación de rol en páginas administrativas; descargas restringidas de proyectos en revisión; cookies de sesión HttpOnly/SameSite; contraseñas verificadas con `password_verify` (la cuenta y su hash pertenecen al núcleo); archivos subidos servidos mediante controlador y directorio sin ejecución PHP.

No introduzcas SQL con concatenación de datos del usuario. Nunca guardes contraseñas en el módulo ni incluyas credenciales reales en Git.

## Pruebas manuales antes de integrar

- Galería vacía y con proyectos; filtro por carrera, año, categoría y búsqueda.
- Envío con convocatoria abierta y rechazo cuando está cerrada o fuera de fecha.
- Rechazo de título, resumen, carrera o autor inválidos en el servidor.
- Rechazo de archivos sobre 20 MB, MIME falso, extensión ejecutable o carga incompleta.
- Ingreso como aspirante y como curador; aspirante no accede a `admin/`.
- Transiciones a revisión, publicado, rechazado; solo publicado puede destacarse.
- Intento de descargar medios privados con sesión insuficiente.
- Doble valoración por la misma cuenta: la segunda debe rechazarse.
- Diseño en 375 px, tableta y escritorio; no debe aparecer desplazamiento horizontal.
- Probar todos los formularios tras actualizar y verificar que no duplican registros al recargar.

## Pendientes de integración / entrega

El repositorio actual no contiene el núcleo del Explorador. Antes de considerar la integración lista, confirmar la ruta real de conexión PDO, el formato de sesión, los nombres/tipos exactos de columnas en `usuarios` y `carreras`, y la correspondencia de roles con el docente. La publicación HTTPS, las cuentas de prueba oficiales y las pruebas de producción dependen de la plataforma integrada.

La entrega de la asignatura incluye además datos suficientes de prueba, credenciales de prueba por rol y reporte de defectos ejecutados. No se crean cuentas ficticias aquí porque la tabla de usuarios la administra el núcleo.

Fechas señaladas en la especificación: entrega del módulo 20 de noviembre de 2026; presentación final 1 de diciembre de 2026.
