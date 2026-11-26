# DinoMaps

Mapa urbano colaborativo de Neuquén. Proyecto de Programación Web Dinámica de la UNCo.

## Desarrollo con Docker

Con Docker Desktop abierto, ejecutá desde la raíz del proyecto.

```sh
docker compose up --build -d
```

La primera ejecución descarga las imágenes y dependencias, genera la clave local y aplica las migraciones. Esperá a que http://127.0.0.1:8000/up responda y cargá el catálogo inicial.

```sh
docker compose exec app php artisan migrate --seed
```

Abrí http://127.0.0.1:8000. Cada integrante usa su propia base MariaDB. El servicio `scheduler` ejecuta la limpieza de avisos vencidos. La aplicación deja de mostrarlos al cumplir 36 horas y el siguiente ciclo del programador los borra.

Si ya tenés los contenedores arriba y descargaste cambios, ejecutá `docker compose up -d` para aplicar los cambios en servicios y migraciones. Para actualizar los datos iniciales, ejecutá `docker compose exec app php artisan db:seed`.

## Compartir una demo por URL

Con Docker Desktop abierto, ejecutá desde la raíz de DinoMaps.

```powershell
docker compose -f compose.demo.yaml up -d --build
docker compose -f compose.demo.yaml logs tunnel
```

Compartí el enlace HTTPS que termina en `trycloudflare.com`. La demo usa un proyecto de Compose y una base separados, con el catálogo inicial. Cualquier persona con el enlace puede entrar. El enlace funciona mientras tu PC, Docker y el túnel estén encendidos.

Para cerrar la demo, ejecutá `docker compose -f compose.demo.yaml stop`. El próximo arranque del túnel genera una URL nueva.

## Desarrollo local con SQLite en Windows

Con PHP 8.4 y Composer disponibles, ejecutá desde PowerShell.

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
composer install
if (-not (Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite }
```

En una instalación nueva, generá la clave con `php artisan key:generate`. En una instalación existente, conservá la clave del archivo `.env`.

```powershell
php artisan migrate --seed
php artisan serve
```

PHP necesita las extensiones `mbstring` y `pdo_sqlite` para este entorno. La ruta `/up` comprueba que Laravel arranca. El mapa necesita conexión para cargar Leaflet y las teselas.

## Recorridos en el mapa

Para habilitar el cálculo interno, configurá `GEOAPIFY_API_KEY` en `.env`. Laravel llama a Geoapify desde el servidor. Conservá la clave fuera del repositorio. Las fichas ofrecen direcciones externas aunque la clave esté vacía.

## Administrar el catálogo

Con Docker activo, aplicá las migraciones y registrá desde la web la cuenta que va a administrar el catálogo. Reemplazá el correo de ejemplo por el de esa cuenta.

```powershell
docker compose exec app php artisan migrate
docker compose exec app php artisan dinomaps:administrador admin@example.com
```

Desde **Cuenta → Gestionar lugares**, el administrador puede agregar, editar y archivar lugares. El archivado oculta el lugar y conserva sus reseñas. Repetir el seeder agrega lugares nuevos y preserva los lugares editados o archivados.

## Documentación del equipo

- [Estado del desarrollo](docs/CURRENT_STATE.md)
- [Guía para la defensa](docs/DEFENSA.md)
- [Prototipo de DinoMaps en Figma](https://www.figma.com/design/3RywgJEOTOEOTCVz3VqsKs/DinoMaps?node-id=1478-547)
