-- Modulo PRO: Portafolio de proyectos
-- Importar despues de crear las tablas del nucleo usuarios y carreras.
-- No crea ni modifica tablas del nucleo.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS pro_convocatorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(140) NOT NULL,
  descripcion TEXT NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_cierre DATE NOT NULL,
  estado ENUM('borrador','abierta','cerrada') NOT NULL DEFAULT 'borrador',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_pro_conv_estado (estado, fecha_inicio, fecha_cierre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pro_categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE,
  activa TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pro_proyectos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  convocatoria_id INT UNSIGNED NULL,
  carrera_id INT UNSIGNED NOT NULL,
  categoria_id INT UNSIGNED NULL,
  autor_usuario_id INT UNSIGNED NULL COMMENT 'Referencia lógica a usuarios.id del núcleo',
  titulo VARCHAR(160) NOT NULL,
  resumen VARCHAR(280) NOT NULL,
  descripcion TEXT NOT NULL,
  tipo VARCHAR(80) NOT NULL,
  anio YEAR NOT NULL,
  estado ENUM('en_revision','publicado','rechazado') NOT NULL DEFAULT 'en_revision',
  destacado TINYINT(1) NOT NULL DEFAULT 0,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  eliminado_en DATETIME NULL,
  CONSTRAINT fk_pro_proyecto_convocatoria FOREIGN KEY (convocatoria_id) REFERENCES pro_convocatorias(id) ON DELETE SET NULL,
  CONSTRAINT fk_pro_proyecto_categoria FOREIGN KEY (categoria_id) REFERENCES pro_categorias(id) ON DELETE SET NULL,
  INDEX idx_pro_publicacion (estado, destacado, anio),
  INDEX idx_pro_filtros (carrera_id, tipo, anio),
  INDEX idx_pro_autor (autor_usuario_id),
  INDEX idx_pro_eliminado (eliminado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pro_autores (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  proyecto_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NULL COMMENT 'Referencia lógica a usuarios.id del núcleo',
  nombre VARCHAR(140) NOT NULL,
  contribucion VARCHAR(180) NULL,
  CONSTRAINT fk_pro_autor_proyecto FOREIGN KEY (proyecto_id) REFERENCES pro_proyectos(id) ON DELETE CASCADE,
  INDEX idx_pro_autor_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pro_medios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  proyecto_id INT UNSIGNED NOT NULL,
  subido_por INT UNSIGNED NULL COMMENT 'Referencia lógica a usuarios.id del núcleo',
  tipo ENUM('imagen','video','pdf') NOT NULL,
  nombre_original VARCHAR(255) NOT NULL,
  nombre_archivo VARCHAR(80) NOT NULL UNIQUE,
  mime VARCHAR(100) NOT NULL,
  tamano BIGINT UNSIGNED NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pro_medio_proyecto FOREIGN KEY (proyecto_id) REFERENCES pro_proyectos(id) ON DELETE CASCADE,
  INDEX idx_pro_medio_proyecto (proyecto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pro_valoraciones (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  proyecto_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NOT NULL COMMENT 'Un usuario con sesion puede valorar una vez cada proyecto',
  puntuacion TINYINT UNSIGNED NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pro_val_proyecto FOREIGN KEY (proyecto_id) REFERENCES pro_proyectos(id) ON DELETE CASCADE,
  CONSTRAINT uq_pro_val_unica UNIQUE (proyecto_id, usuario_id),
  CONSTRAINT chk_pro_val_rango CHECK (puntuacion BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO pro_categorias (nombre) VALUES
('Proyecto integrador'), ('Receta y producto'), ('Emprendimiento'), ('Investigacion'), ('Practica profesional');

-- Datos de demostracion iniciales. El proyecto se inserta solo si existe la carrera Gastronomia.
INSERT INTO pro_convocatorias (titulo, descripcion, fecha_inicio, fecha_cierre, estado)
SELECT 'Convocatoria de muestra 2026', 'Registro de demostracion para probar el flujo de proyectos.', '2026-10-01', '2026-11-20', 'abierta'
WHERE NOT EXISTS (SELECT 1 FROM pro_convocatorias WHERE titulo='Convocatoria de muestra 2026');

INSERT INTO pro_proyectos (convocatoria_id,carrera_id,categoria_id,titulo,resumen,descripcion,tipo,anio,estado,destacado)
SELECT cv.id, ca.id, ct.id, 'Cocina regional sostenible (muestra)',
       'Ejemplo de ficha para revisar la galería y sus filtros.',
       'Este es un proyecto demostrativo. Sustitúyanlo por contenido autorizado del equipo antes de la presentación final.',
       'Proyecto integrador', 2026, 'publicado', 1
FROM carreras ca
JOIN pro_convocatorias cv ON cv.titulo='Convocatoria de muestra 2026'
LEFT JOIN pro_categorias ct ON ct.nombre='Proyecto integrador'
WHERE ca.nombre LIKE '%Gastronomía%'
  AND NOT EXISTS (SELECT 1 FROM pro_proyectos WHERE titulo='Cocina regional sostenible (muestra)');
