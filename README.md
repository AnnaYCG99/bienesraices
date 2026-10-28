# Bienes Raíces

Sitio web de bienes raíces hecho con **PHP**, **MySQL/MariaDB**, **Sass** y **Gulp**. Incluye un panel de administración para crear, actualizar y eliminar propiedades, y una página pública que muestra los anuncios guardados en la base de datos.

## Requisitos

Antes de empezar, instala lo siguiente:

| Herramienta | Versión recomendada | Para qué se usa |
|---|---|---|
| [Git](https://git-scm.com/) o GitHub Desktop | Cualquiera reciente | Clonar el repositorio |
| [Node.js](https://nodejs.org/) | 18 o superior | Compilar Sass y JavaScript con Gulp |
| [PHP](https://www.php.net/) | 8.2 o superior (probado con 8.4) | Ejecutar el sitio |
| MySQL o MariaDB | 5.7 / 10.4 o superior | Base de datos |
| MySQL Workbench o TablePlus | Opcional | Administrar la base de datos |

En Windows puedes instalar PHP con:

```powershell
winget install PHP.PHP.8.4
```

Después cierra y vuelve a abrir la terminal, y comprueba con `php -v`.

### Activar la extensión mysqli de PHP

PHP instalado con winget no trae `php.ini`, y sin él el sitio falla con el error `Call to undefined function mysqli_connect()`. Para activarla:

1. Busca la carpeta de PHP (en Windows, dentro de `%LOCALAPPDATA%\Microsoft\WinGet\Packages`).
2. Copia `php.ini-development` como `php.ini`.
3. En `php.ini`, quita el `;` del inicio de estas dos líneas:
   ```ini
   extension_dir = "ext"
   extension=mysqli
   ```
4. Comprueba con `php -m` que aparezca `mysqli`.

## Instalación

### 1. Clonar el repositorio

Con la terminal:

```bash
git clone https://github.com/AnnaYCG99/bienesraices.git
cd bienesraices
```

O con GitHub Desktop: **File → Clone repository**, elige `bienesraices` y presiona **Clone**.

> **Importante:** clona el proyecto **fuera de OneDrive** (por ejemplo en `C:\proyectos`). OneDrive bloquea archivos mientras sincroniza y provoca errores al instalar las dependencias.

### 2. Instalar las dependencias

```bash
npm install
```

Los avisos `npm warn deprecated` y las vulnerabilidades que reporta se pueden ignorar en este proyecto.

### 3. Configurar la conexión a la base de datos

El archivo con las credenciales (`includes/config/database.php`) **no está en el repositorio** por seguridad. Cópialo desde el ejemplo:

```bash
# Windows (PowerShell)
Copy-Item includes/config/database.example.php includes/config/database.php

# Mac / Linux
cp includes/config/database.example.php includes/config/database.php
```

Abre `includes/config/database.php` y reemplaza `TU_CONTRASEÑA` por la contraseña de tu usuario de MySQL. Si tu servidor usa un usuario o puerto distinto, ajústalos también:

```php
$db = mysqli_connect('localhost', 'root', 'TU_CONTRASEÑA', 'bienesraices_crud');
// Con otro puerto, agrégalo como quinto parámetro:
// $db = mysqli_connect('localhost', 'root', 'TU_CONTRASEÑA', 'bienesraices_crud', 3307);
```

### 4. Crear la base de datos

El repositorio incluye el archivo `bienesraices.sql` con la estructura (tablas `vendedores` y `propiedades`) y los datos de ejemplo.

**Con MySQL Workbench:** abre tu conexión local y ve a **Server → Data Import**. Elige **Import from Self-Contained File**, selecciona `bienesraices.sql` y, en *Default Target Schema*, crea una base llamada `bienesraices_crud`. Presiona **Start Import**.

**Con la terminal:**

```bash
mysql -u root -p -e "CREATE DATABASE bienesraices_crud CHARACTER SET utf8mb4;"
mysql -u root -p bienesraices_crud < bienesraices.sql
```

Comprueba que se haya creado con:

```sql
SELECT * FROM bienesraices_crud.vendedores;
```

Debes ver a los vendedores de ejemplo con los ids `1` y `2`. El formulario de propiedades usa esos ids, así que no los borres ni los recrees.

### 5. Crear la carpeta de imágenes

Las fotos de las propiedades se guardan en `imagenes/`. Si la carpeta no existe después de clonar, créala en la raíz del proyecto:

```bash
mkdir imagenes
```

## Ejecutar el proyecto

Necesitas **dos terminales abiertas** al mismo tiempo, ambas dentro de la carpeta del proyecto.

**Terminal 1: compilar estilos y JavaScript**

```bash
npm run dev
```

Gulp compila Sass, JavaScript e imágenes, y se queda vigilando los cambios. Déjala abierta.

**Terminal 2: servidor de PHP**

```bash
php -S localhost:3000
```

Abre en el navegador:

- Sitio público: <http://localhost:3000>
- Panel de administración: <http://localhost:3000/admin>

Para detener cualquiera de los dos procesos, presiona `Ctrl + C` en su terminal.

## Estructura del proyecto

```
bienesraices/
├── admin/               Panel de administración (listar, crear, actualizar, eliminar)
├── build/               Archivos compilados (CSS, JS, imágenes). Los genera Gulp
├── imagenes/            Fotos subidas desde el formulario de propiedades
├── includes/
│   ├── config/          Conexión a la base de datos
│   ├── templates/       Header, footer y bloque de anuncios
│   └── funciones.php    Funciones auxiliares
├── src/                 Código fuente: SCSS, JavaScript e imágenes originales
├── gulpfile.js          Tareas de Gulp
├── package.json         Dependencias de Node
└── *.php                Páginas públicas (index, nosotros, anuncios, contacto...)
```

> No edites los archivos de `build/`: se regeneran con Gulp. Los cambios de estilos van en `src/scss/` y los de JavaScript en `src/js/`.

## Problemas frecuentes

**`Access denied for user 'root'@'localhost'`**
La contraseña de `includes/config/database.php` no coincide con la de tu MySQL. Compruébala entrando a MySQL Workbench o TablePlus con esos mismos datos.

**`Call to undefined function mysqli_connect()`**
La extensión `mysqli` no está activada. Sigue la sección *Activar la extensión mysqli de PHP* y reinicia el servidor de PHP.

**`Connection refused` o `No connection could be made`**
El servidor de MySQL está apagado o usa otro puerto. Revisa el puerto en tu cliente de base de datos y agrégalo en `database.php`.

**`Unknown database 'bienesraices_crud'`**
Todavía no creaste o importaste la base de datos (paso 4).

**La página se ve sin estilos**
Gulp no está corriendo o no ha terminado de compilar. Ejecuta `npm run dev` y recarga el navegador con `Ctrl + F5`.

**`Can't find stylesheet to import` o `Undefined variable` al ejecutar Gulp**
Algún archivo de `src/scss/` tiene un nombre mal escrito o falta. Los parciales de Sass deben empezar con guion bajo (`_header.scss`) y coincidir con los `@import` de `src/scss/app.scss`.

**Aparece un aviso `Deprecated: Using ${var} in strings`**
Es un aviso de PHP 8.2 o superior. No impide que el sitio funcione, y se corrige cambiando `${var}` por `{$var}` en el archivo que lo muestra.

**No se pueden subir imágenes**
La carpeta `imagenes/` no existe o no tiene permisos de escritura.

## Trabajar en equipo

Antes de empezar a trabajar, trae los últimos cambios del repositorio:

```bash
git pull
```

Cuando termines un cambio:

```bash
git add .
git commit -m "Describe lo que cambiaste"
git push
```

Con GitHub Desktop: **Fetch origin** y **Pull** antes de empezar, y **Commit to main** y **Push origin** al terminar. Lo más ordenado es que cada persona trabaje en su propia rama y la mezcle mediante un Pull Request.

**Nunca subas tu `includes/config/database.php`.** Contiene tu contraseña y ya está en el `.gitignore`.
