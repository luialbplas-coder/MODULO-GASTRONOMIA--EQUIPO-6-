# Módulo de gastronomía

Repositorio del equipo para organizar y desarrollar el proyecto escolar del módulo de gastronomía. Aquí guardaremos el código, los avances y la documentación del equipo.

## Organización sugerida

Mantengan los archivos fáciles de encontrar. Por ejemplo:

```text
MODULO-GASTRONOMIA--EQUIPO-6-/
├── index.html
├── css/
│   └── estilos.css
├── js/
│   └── app.js
├── img/
├── docs/
└── README.md
```

Ajusten las carpetas según lo que pida el proyecto. Usen nombres claros y eviten subir archivos temporales, contraseñas o datos personales.

## Preparar Git en cada computadora

Instalen Git y configuren su nombre y correo una sola vez en cada computadora:

```bash
git config --global user.name "Tu nombre"
git config --global user.email "tu-correo@example.com"
```

Clonen el repositorio una sola vez. Reemplacen la URL por la dirección HTTPS que aparece en el botón **Code** del repositorio:

```bash
git clone https://github.com/luialbplas-coder/MODULO-GASTRONOMIA--EQUIPO-6-.git
cd MODULO-GASTRONOMIA--EQUIPO-6-
```

También pueden abrir la carpeta clonada en Visual Studio Code y usar **Terminal → New Terminal** para escribir los comandos.

## Flujo de trabajo recomendado

Antes de empezar a trabajar, actualicen su copia local:

```bash
git switch main
git pull origin main
```

Hagan cambios pequeños y relacionados. Después revisen qué cambió:

```bash
git status
git diff
```

Agreguen los archivos que quieren guardar y creen un commit con un mensaje descriptivo:

```bash
git add index.html css/estilos.css js/app.js
git commit -m "Agrega estructura inicial de la página"
```

Para agregar todos los cambios actuales, pueden usar `git add .`, pero revisen primero `git status` para no incluir archivos que no deben compartirse.

Suban sus commits a GitHub:

```bash
git push origin main
```

### ¿Qué significa cada comando?

| Comando | Para qué sirve |
| --- | --- |
| `git clone URL` | Descarga el repositorio y crea una copia local. |
| `git status` | Muestra archivos modificados, preparados y pendientes. |
| `git diff` | Permite revisar los cambios antes de guardarlos. |
| `git add archivo` | Prepara un archivo para incluirlo en el siguiente commit. |
| `git commit -m "mensaje"` | Guarda un punto de control local con una descripción. |
| `git pull origin main` | Descarga e integra los cambios de la rama main. |
| `git push origin main` | Publica en GitHub los commits locales. |
| `git log --oneline` | Muestra el historial resumido de commits. |

**Pull descarga e integra cambios; push publica los cambios locales.** Un commit todavía no aparece en GitHub hasta hacer push.

## Trabajar con ramas (recomendado si varias personas editan)

Cada integrante puede trabajar en una rama para una tarea. Primero actualicen main y creen su rama:

```bash
git switch main
git pull origin main
git switch -c feature/menu-principal
```

Guarden y publiquen los cambios de esa rama:

```bash
git add .
git commit -m "Crea menú principal"
git push -u origin feature/menu-principal
```

Después, en GitHub, creen un **Pull Request** de esa rama hacia `main`. Otro integrante puede revisar los cambios y entonces se integran. Para volver a trabajar en main después de integrar:

```bash
git switch main
git pull origin main
```

Usen nombres de rama breves y claros, por ejemplo `feature/recetas`, `fix/menu-movil` o `docs/instrucciones`.

## Si Git avisa que hay cambios remotos

Si alguien subió cambios mientras trabajaban, guarden primero su trabajo en un commit y luego integren lo nuevo:

```bash
git add .
git commit -m "Guarda avance de la sección de recetas"
git pull --rebase origin main
git push origin main
```

Si aparece un conflicto, Git indicará los archivos afectados. Abran cada archivo, decidan qué contenido debe conservarse y eliminen los marcadores `<<<<<<<`, `=======` y `>>>>>>>`. Luego:

```bash
git add archivo-resuelto
git rebase --continue
```

Si no saben cómo resolverlo, no borren ni sobrescriban los cambios: pidan apoyo al equipo y revisen juntos el archivo.

## Ejemplos básicos de la página

### HTML — `index.html`

HTML organiza el contenido. Este ejemplo usa etiquetas semánticas y enlaza el CSS y JavaScript:

```html
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo de gastronomía</title>
  <link rel="stylesheet" href="css/estilos.css">
  <script src="js/app.js" defer></script>
</head>
<body>
  <header class="encabezado">
    <h1>Sabores de nuestra región</h1>
    <nav aria-label="Navegación principal">
      <a href="#platillos">Platillos</a>
      <a href="#equipo">Equipo</a>
    </nav>
  </header>

  <main>
    <section id="platillos" class="tarjeta">
      <h2>Platillo destacado</h2>
      <p>Escribe aquí una descripción breve del platillo.</p>
      <button id="botonInfo" type="button">Ver mensaje</button>
      <p id="mensaje" aria-live="polite"></p>
    </section>
  </main>

  <footer id="equipo">
    <p>Proyecto escolar · Equipo 6</p>
  </footer>
</body>
</html>
```

### CSS — `css/estilos.css`

CSS controla la presentación visual:

```css
:root {
  font-family: Arial, sans-serif;
  color: #2b2118;
  background: #fff8ed;
}

* {
  box-sizing: border-box;
}

body {
  margin: 0;
  line-height: 1.6;
}

.encabezado {
  padding: 1.5rem;
  color: white;
  background: #7b2d26;
}

nav a {
  margin-right: 1rem;
  color: white;
}

main {
  width: min(900px, 92%);
  margin: 2rem auto;
}

.tarjeta {
  padding: 1.5rem;
  border: 1px solid #ead8bd;
  border-radius: 12px;
  background: white;
}

button {
  padding: 0.7rem 1rem;
  border: 0;
  border-radius: 6px;
  color: white;
  background: #356859;
  cursor: pointer;
}

button:hover {
  background: #244b40;
}

footer {
  padding: 1rem;
  text-align: center;
}
```

### JavaScript — `js/app.js`

JavaScript agrega interacción a la página:

```javascript
const boton = document.querySelector("#botonInfo");
const mensaje = document.querySelector("#mensaje");

boton.addEventListener("click", () => {
  mensaje.textContent = "¡Gracias por conocer nuestro proyecto gastronómico!";
});
```

## Hábitos para mantener el proyecto ordenado

- Antes de comenzar, hagan `git pull origin main`.
- Guarden avances con commits pequeños y mensajes que expliquen el cambio.
- Antes de hacer push, revisen `git status` y prueben la página en el navegador.
- No editen el mismo archivo al mismo tiempo sin coordinarse.
- No suban contraseñas, tokens, archivos privados ni carpetas pesadas como `node_modules`.
- Si usan imágenes, nombren los archivos con claridad y respeten sus permisos de uso.
- Mantengan este README actualizado si cambia la forma de trabajar.


## Proyecto del equipo A-6

El módulo asignado es **PRO · Portafolio de proyectos** para el Explorador Vocacional UTBB. La carrera de Gastronomía aporta contenido, pero la galería puede incluir proyectos de todas las carreras. El código del módulo está en [modulos/portafolio](modulos/portafolio/README.md). Sigue sus pasos de instalación; el núcleo de la plataforma aún debe integrarse desde el repositorio oficial compartido.
