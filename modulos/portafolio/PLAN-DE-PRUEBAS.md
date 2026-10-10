# Plan de pruebas y registro de defectos

Módulo PRO · Equipo A-6 · Versión de trabajo

**Estado:** matriz preparada; pruebas todavía pendientes en XAMPP con el núcleo de la plataforma. No marcar como aprobada una prueba hasta ejecutarla y anotar evidencia.

| ID | Caso | Resultado esperado | Resultado observado / evidencia | Estado |
|---|---|---|---|---|
| UI-01 | Abrir galería a 375 px | No hay desplazamiento horizontal; filtros y tarjetas caben en pantalla | Pendiente | Pendiente |
| PUB-01 | Abrir proyecto publicado | Ficha muestra descripción, autores, medios y promedio de valoración | Pendiente | Pendiente |
| PUB-02 | Filtrar por carrera, año, tipo, categoría y texto | Solo aparecen coincidencias; entradas inválidas no alteran la consulta | Pendiente | Pendiente |
| AUTH-01 | Aspirante intenta entrar a cada ruta de administración | Recibe 403 y no ve ni modifica datos del panel | Pendiente | Pendiente |
| AUTH-02 | Editor inicia sesión con hash válido | Entra al panel; contraseña incorrecta no inicia sesión | Pendiente | Pendiente |
| PRO-01 | Aspirante envía formulario válido con convocatoria abierta | Se crea proyecto en_revision y se redirige con mensaje de confirmación | Pendiente | Pendiente |
| PRO-02 | Envío con convocatoria cerrada o fuera de fechas | No se crea proyecto | Pendiente | Pendiente |
| PRO-03 | Editor edita, publica, rechaza, destaca y da de baja proyecto | Cambios quedan en BD; solo publicados se muestran y pueden destacarse | Pendiente | Pendiente |
| CON-01 | Editor crea, edita y elimina convocatoria | CRUD completo; al eliminar, los proyectos relacionados quedan sin convocatoria | Pendiente | Pendiente |
| CAT-01 | Editor crea y oculta/reactiva categoría | Categoría se guarda; las ocultas no aparecen en filtros ni formularios | Pendiente | Pendiente |
| FILE-01 | Subir JPG/PNG/WebP/MP4/PDF válido de hasta 20 MB | MIME real validado; nombre aleatorio en disco; medio asociado al proyecto | Pendiente | Pendiente |
| FILE-02 | Subir archivo mayor a 20 MB, MIME falso o PHP renombrado | Servidor lo rechaza y no lo publica | Pendiente | Pendiente |
| FILE-03 | Aspirante ajeno intenta descargar medio de proyecto en revisión | Recibe 403; editor y autor permitido sí acceden | Pendiente | Pendiente |
| RATE-01 | Aspirante valora proyecto publicado | Se guarda puntuación entre 1 y 5 | Pendiente | Pendiente |
| RATE-02 | Misma cuenta vuelve a valorar mismo proyecto | Restricción única impide segunda valoración | Pendiente | Pendiente |
| SEC-01 | Enviar etiqueta HTML/script en título, nombre o búsqueda | Se muestra escapada; no se ejecuta script | Pendiente | Pendiente |
| SEC-02 | Alterar valores de formulario, ID o token CSRF | Servidor valida rol, propiedad, rango, relación y token; no hay acceso indebido | Pendiente | Pendiente |
| FLOW-01 | Recargar después de una operación POST | No repite inserción/cambio; se aplica POST-Redirect-GET | Pendiente | Pendiente |
| DB-01 | Buscar texto con comillas y caracteres SQL | No cambia la consulta ni se filtran registros de más | Pendiente | Pendiente |

## Registro de ejecución

Por cada prueba, anotar fecha, responsable, navegador/resolución, resultado y una captura o referencia a los datos usados. Si falla, registrar el defecto con severidad:

- **Crítica:** acceso no autorizado, pérdida/exposición de datos o corrupción.
- **Alta:** función esencial bloqueada o requisito de seguridad incumplido.
- **Media:** función disponible con error o recorrido alternativo necesario.
- **Baja:** detalle visual o de texto sin impacto funcional importante.

| Defecto | Prueba relacionada | Severidad | Pasos para reproducir | Corrección / responsable | Estado |
|---|---|---|---|---|---|
| — | — | — | — | — | — |

Antes de la entrega, ejecutar las pruebas en producción HTTPS para páginas restringidas, permisos por rol y manipulación de identificadores, como solicita la especificación general.
