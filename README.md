# Sistema de Actas

Aplicación web para que los inspectores carguen actas de infracción desde el celular, tanto actas simples como actas dentro de un operativo de control.

## Stack

- PHP 8.2 · Laravel 12
- Livewire 3 (componentes en `app/Livewire`) · Tailwind CSS · Vite
- MySQL (base `munimer_faltas`)
- SweetAlert2 (CDN) para confirmaciones y avisos

## Funcionalidades

| Ruta | Componente | Descripción |
|---|---|---|
| `/login` | `AuthenticatedSessionController` | Ingreso con DNI y contraseña del inspector |
| `/actas` | `DashboardActas` | Panel principal |
| `/actas/crear-simple` | `CrearActa` | Acta simple, sin operativo |
| `/actas/operativo/{id}` | `ControlOperativo` | Registro de controles dentro de un operativo |
| `/actas/operativo/{id}/acta` | `CrearActa` | Acta dentro de un operativo |
| `/actas/listar` | `ListarActas` | Actas del inspector |
| `/actas/{acta}/editar` | `EditarActa` | Edición de un acta |

- **Motivos**: cada acta requiere al menos un motivo de infracción (`fa_motivo`), filtrado por el departamento del inspector.
- **Actas simples**: se guardan con `operativo_id = -1` (`Acta::OPERATIVO_ACTA_SIMPLE`) para distinguirlas de las cargadas por otros medios, que tienen `NULL`. Las actas de operativo guardan el id del operativo (`> 0`).
- **Confirmación del vehículo**: al guardar un acta nueva se muestra un cartel con la patente y los datos del vehículo para que el inspector confirme que son correctos.
- **Fotos**: hasta 5 por acta, en JPG. Se previsualizan al cargarlas.

### Fotos

- En el navegador (`resources/js/comprimir-fotos.js`) se achican a 1920px de lado mayor, se recomprimen a JPEG y se corrige la rotación EXIF antes de subirlas. Se activa con el atributo `data-comprimir-foto` en el `<input type="file">`.
- En el servidor, si igual llegan con más de 2MB, se vuelven a comprimir (`comprimirFotoSiEsNecesario`) y al guardar se re-generan con GD.
- Se guardan en `public/fotos/` como `fot-{nº de acta con 10 dígitos}-{nº de foto con 3 dígitos}.jpg` (ej.: `fot-0000005557-002.jpg`).
- `public/fotos/` está en `.gitignore` salvo su `.htaccess`, que impide ejecutar scripts en esa carpeta y **tiene que estar en cada servidor**.

## Autenticación

No se usa la tabla `users`. El login es con el guard `inspector`, sobre la tabla `fa_inspector` (DNI + contraseña). No hay registro público: los inspectores se dan de alta directamente en la base.

> ⚠️ Las contraseñas de `fa_inspector` se guardan y comparan en texto plano. Está pendiente migrar a hash.

## Base de datos

La mayoría de las tablas son preexistentes y **no se crean con migraciones**: `fa_acta`, `fa_acta_motivo`, `fa_inspector`, `fa_motivo`, `fa_persona`, `fa_departamento`, `fa_marca`, `fa_tiporodado` y `operativos`. Para desarrollo hace falta una copia de la base `munimer_faltas`.

Las migraciones del repo solo crean las tablas de Laravel (`users`, `cache`, `jobs`) y `fa_registro_control`.

## Instalación

```bash
composer install
cp .env.example .env        # configurar DB_CONNECTION=mysql y los datos de munimer_faltas
php artisan key:generate
php artisan migrate
npm install
npm run build
```

En Apache, `public/.htaccess` sube los límites de PHP (`upload_max_filesize 10M`, `post_max_size 12M`) para la subida de fotos.

## Desarrollo

- `npm run dev` para trabajar con Vite en caliente.
- **`public/build` está versionado**: después de cambiar JS o CSS (o clases de Tailwind en las vistas), correr `npm run build` y commitear los archivos generados junto con el cambio.

## Deploy

Hay un servidor de prueba y uno oficial. Probar primero en el de prueba. En cada servidor, desde la carpeta del proyecto:

```bash
git status                  # revisar que no haya cambios locales inesperados
git pull origin master
php artisan optimize:clear
```

- **No correr `npm run build` en los servidores**: el build viene commiteado. Si alguien lo corrió, `public/build` aparece modificado y el `pull` se frena; se resuelve con `git checkout -- public/build` y borrando el `.css` suelto que quede como untracked en `public/build/assets/`.
- `public/storage` es un enlace simbólico versionado que en el repo apunta a `/var/www/actas/storage/app/public`. En los servidores donde el proyecto está en otra ruta aparece como modificado porque se corrigió a mano: **no restaurarlo** (no usar `git checkout -- .` ni `git restore .`).
- Solo hace falta `composer install` o `php artisan migrate` si el cambio lo requiere (dependencias o migraciones nuevas).

## Pendientes

- Guardar las contraseñas de `fa_inspector` con hash (confirmar antes si otro sistema usa esa tabla).
- Sacar `public/storage` del repo y crearlo en cada servidor con `php artisan storage:link`.
