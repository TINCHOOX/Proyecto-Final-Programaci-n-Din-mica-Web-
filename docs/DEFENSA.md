# DinoMaps · Guía de entrega y defensa

DinoMaps es un mapa urbano colaborativo de Neuquén capital. Permite encontrar lugares, consultar reseñas y ver avisos comunitarios sobre lo que pasa en las calles. Proyecto de Programación Web Dinámica de la UNCo.

**Integrantes:** Erick Clavería. Completar con los nombres y apellidos de los demás integrantes antes de entregar.

## Funcionalidades

- Mapa interactivo con búsqueda, siete categorías, filtros de Turismo, fichas y marcadores.
- Cuenta, reseñas con valoraciones, avisos de tránsito con confirmaciones, puntos y colección de dinosaurios.
- Tema claro y oscuro, modos de color para daltonismo y diseño adaptable a celular.
- Panel de catálogo para consultar, crear, editar y archivar lugares. El archivado retira el lugar de las vistas públicas y conserva sus reseñas.

## Perfiles y permisos

| Perfil | Permisos |
| --- | --- |
| Visitante | Consulta el mapa y el catálogo, y puede registrarse o iniciar sesión. |
| Usuario registrado | Además, publica reseñas y avisos, vota siguiendo las reglas de autoría y canjea o equipa dinosaurios. Puede retirar sus propios avisos. |
| Administrador | Tiene los permisos del usuario registrado y puede gestionar lugares desde Cuenta. |

Una cuenta registrada desde la web nace como usuario común. Para el ensayo, registrá dos cuentas distintas y habilitá una de ellas desde la terminal del proyecto.

~~~powershell
docker compose exec app php artisan migrate
docker compose exec app php artisan dinomaps:administrador correo-de-la-cuenta
~~~

El comando promociona una cuenta existente. En la web, el middleware de autenticación pide iniciar sesión. El middleware de autorización consulta el Gate de catálogo y devuelve HTTP 403 al usuario común. Blade usa el mismo permiso para mostrar **Gestionar lugares**. Probá ambos perfiles en ventanas separadas. Si usás el Compose de demo, ejecutá el comando con el archivo Compose de demo para asignar el permiso en esa base.

## Modelo de datos

| Relación | Eloquent | Dónde mostrarla |
| --- | --- | --- |
| Lugar → reseñas (1:N) | Lugar::reviews, Review::lugar | Una ficha lista reseñas y calcula su distribución de estrellas. |
| Usuario → reseñas (1:N) | Review::user | La ficha muestra el autor de cada reseña. |
| Categoría → lugares (1:N) | Lugar::categoria | Un filtro de categoría selecciona los lugares que pertenecen a ella. |
| Usuario ↔ dinosaurios (N:M) | User::dinosaurs, Dinosaur::owners | Un usuario canjea un dinosaurio y aparece en su colección. La asociación se guarda en dinosaur_user mediante attach. |

El canje bloquea la fila del usuario dentro de una transacción. Consulta si ya tiene el dinosaurio, verifica el saldo, descuenta el precio y crea la asociación. La tabla intermedia tiene una clave compuesta que impide duplicados. Los avisos y las reseñas acreditan 100 puntos una sola vez al recibir la segunda valoración válida de otra cuenta.

## MVC, AJAX y librerías externas

- **MVC.** Las rutas envían la solicitud al controlador. Eloquent consulta y modifica MariaDB. Blade presenta los resultados. Para el panel de lugares, GuardarLugarRequest valida los datos antes de que el controlador guarde.
- **AJAX.** El mapa usa fetch para pedir JSON a GET /mapa/puntos, con los mismos filtros que el listado. Lee las coordenadas y crea los marcadores sin recargar la pantalla. Ante una respuesta fallida muestra un aviso y el catálogo sigue disponible. En la defensa, abrí DevTools → Red → Fetch/XHR para mostrar la solicitud, su JSON y los marcadores correspondientes. Las confirmaciones de tránsito también actualizan la interfaz mediante fetch.
- **Leaflet.** Dibuja y permite explorar en el mapa los lugares georreferenciados de nuestra base. MapLibre aporta la cartografía vectorial de OpenFreeMap. **Chart.js** usa la distribución de calificaciones reales en la ficha y la acompaña una tabla con los mismos datos. La librería externa elegida para presentar debe estar aprobada por la cátedra cuando corresponda.

## Orden de la demostración

1. Presenten DinoMaps en una frase. En el mapa, elijan una categoría, abran un marcador y entren a su ficha. Muestren la solicitud AJAX y expliquen qué aporta Leaflet.
2. Inicien sesión como usuario común. Comprueben que Cuenta no ofrece **Gestionar lugares** y que al entrar a /admin/lugares se recibe HTTP 403.
3. Entren como administrador. Desde Cuenta → Gestionar lugares, creen un lugar de prueba, abran su ficha pública, edítenlo y archívenlo. Comprueben que deja de aparecer en el catálogo y, si tenía coordenadas, en el mapa.
4. Abran una ficha con reseñas y muestren la relación 1:N y el gráfico de Chart.js. Demuestren el canje con una cuenta que ya tenga saldo y abran Mi colección para explicar la tabla intermedia.
5. Si hay tiempo, usen tres cuentas de prueba para publicar y confirmar un aviso. El primer voto mantiene el saldo y el segundo acredita 100 puntos.

## Entrega

El [README](../README.md) explica cómo iniciar el proyecto y cargar el catálogo con migraciones y seeders. Completen los integrantes en este documento. La consigna pide subir a PEDCO el enlace del repositorio GitHub con el código, las migraciones, los seeders y esta documentación. Antes de enviarlo, comprueben desde otra cuenta que la cátedra pueda abrir el repositorio.
