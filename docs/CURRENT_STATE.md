# DinoMaps · Estado del desarrollo

Actualizado el 25 de noviembre de 2026. Base publicada en `4d2e6ac`. La primera implementación local se apoya en el [mapa de Proyecto Dinámica](https://www.figma.com/design/3RywgJEOTOEOTCVz3VqsKs/DinoMaps?node-id=1478-548).

## Primera implementación

- Visitante: mapa, siete categorías más Todos, búsqueda, Ver todo y fichas con dirección, fuente y enlace de indicaciones.
- Base de datos: 23 lugares en siete categorías. Veinte tienen coordenadas documentadas. Los tres lugares sin coordenadas siguen disponibles en el listado y sus fichas.
- Cuenta: registro, inicio y cierre de sesión. El panel muestra el acceso de visitante o el perfil autenticado según la sesión.
- El navegador usa Leaflet 1.9.4 para interacción, OpenFreeMap Positron mediante MapLibre para cartografía y Fetch para consultar los marcadores de Laravel. La geolocalización se inicia al pulsar Mi ubicación y se usa en el navegador.
- El frame principal y los dos estados de Cuenta se consultaron en Figma. Se integraron los primeros ocho dinosaurios de gilada.zip y se exportaron desde Figma la marca de montaña, los seis íconos de interfaz y las ocho ilustraciones restantes. Los avatares del mapa se identifican como demo.

## Comprobaciones

- Laravel y SQLite en memoria: migraciones, carga repetible del catálogo, vista del mapa, búsqueda combinada con categoría, estado vacío, grilla, ficha por slug, JSON de marcadores, validación y formularios con CSRF.
- Sesiones: pruebas sobre Laravel del registro y hash, inicio correcto e incorrecto, rotación de sesión y cierre.
- Docker con MariaDB: Erick ejecutó `docker compose exec app php artisan migrate --seed`. La migración del catálogo terminó correctamente y el comando de carga inicial volvió a PowerShell sin errores visibles. El navegador mostraba SQLite porque Artisan quitaba las variables de Docker al lanzar el servidor web con recarga automática. Docker Compose arranca ahora con `serve --no-reload`. Erick recreó el contenedor app. Queda comprobar el mapa y la cuenta en el navegador con la última revisión.

## Actividades de Turismo

- El mapa y el catálogo de Turismo comparten los filtros Todas, Trekking, Running y Paseo y parques. La navegación, la búsqueda y la paginación conservan la actividad. El catálogo completo se abre desde Ver todo.
- Las tarjetas abren un detalle con fotos e indicadores según los datos disponibles. La ficha completa conserva las reseñas. Ver en el mapa centra y abre el marcador de los lugares con coordenadas, sin clave de Geoapify.
- La migración clasifica Parque Jaime de Nevares y Plazoleta Junta de Estudios Históricos en Paseo y parques. Solo la plazoleta tiene coordenadas entre esos dos parques. Trekking incluye senderismo urbano en Paseo de la Costa y Running incluye el circuito Blanco Bardas Norte. Los marcadores indican el sector o un punto de referencia, con fuentes en el seeder y en las fichas.
- Verificado con PHPUnit y SQLite en memoria. Las pruebas cubren el filtro combinado con búsqueda en ambas vistas y en el JSON de marcadores, una actividad inválida, el aislamiento de Hoteles, los parques existentes y el estado vacío.
- **Validación de Turismo cerrada el 25 de noviembre de 2026.** La ficha final registra las pruebas de DeepSeek en Chrome, incluidas la navegación a 390 px, el retorno del foco al cerrar con Escape, los estados vacíos, los marcadores ocultos y la regresión de Farmacias. La revisión de integración confirmó los seis archivos, comprobó las referencias y la sintaxis de las vistas y del JavaScript, y corrigió el mensaje residual al quitar un enlace de ubicación inválido. Quedan fuera de la validación los lectores de pantalla y otros motores de navegador.

## Estrellas, tema y versión móvil

- El mapa y el catálogo muestran el promedio de las reseñas con estrellas y un valor numérico accesible. El promedio se calcula con Eloquent en la consulta de listado. Los lugares sin reseñas muestran su estado sin estrellas.
- Configuración guarda el tema claro u oscuro en el navegador de forma independiente del modo de daltonismo. El mapa vectorial y los gráficos actualizan su paleta al cambiar de tema. Los mapas híbrido y satelital conservan los colores de la imagen.
- En celular se apilan el mapa y los resultados, con enlaces para ir de uno a otro. Las ocho opciones de categorías se distribuyen en dos filas. Los lugares nuevos usan los iconos existentes por categoría y se muestran sin foto cuando no hay un activo disponible.
- El seeder amplía el catálogo con 13 lugares. Para cargar los nuevos registros en una instalación existente, ejecutar `docker compose exec app php artisan db:seed`. Este cambio no agrega migraciones.
- Verificado con PHPUnit y SQLite en memoria (6 pruebas, 133 aserciones). Chrome renderizó nueve vistas de Laravel a 360, 390 y 430 px sin desborde horizontal, con dos filas de categorías, tema persistente, daltonismo y retorno del foco en el diálogo. La prueba de interfaz usó recursos locales. Queda verificar la nueva carga en MariaDB y la cartografía externa en vivo.

## Tránsito y confirmaciones

- La pantalla de [Tránsito](https://www.figma.com/design/3RywgJEOTOEOTCVz3VqsKs/DinoMaps?node-id=1521-4848) combina cuatro filtros en un mapa Leaflet. Los cortes publicados se muestran junto a las obras. Los avisos tienen 36 horas de vigencia. Alta concurrencia usa un tramo naranja discontinuo entre dos puntos indicados por el autor. El recorrido de ejemplo muestra trazos ilustrativos, identificados aparte de los avisos comunitarios. El tráfico externo sigue pendiente.
- Un usuario autenticado puede publicar eligiendo tipo, ubicación en el mapa y descripción. Desde el detalle, otras cuentas pueden confirmar que sigue vigente. La segunda confirmación distinta acredita 100 puntos una vez. El saldo se muestra en Cuenta. El autor no puede confirmar su propio aviso y un aviso vencido ya no acepta votos. El autor puede eliminarlo desde el mapa o Mis reportes. El programador de Laravel purga avisos vencidos cada minuto. Los puntos ya acreditados se conservan.
- Verificado con Laravel y SQLite: render del mapa y Tránsito, acceso público de lectura, protección de escritura, publicación de corte, confirmaciones, rechazo de autovoto y voto repetido, premio al segundo voto y saldo persistente. Erick confirmó que MariaDB aplicó la migración de Tránsito en Docker. El recorrido en el navegador queda pendiente.
- Se ajustaron las proporciones del encabezado, panel y categorías con el diseño de Sprint 2. La presentación de identidad confirma la paleta, tipografías y papel de los dinosaurios. El PowerPoint contiene capturas de las pantallas. Desde el ZIP se incorporaron ocho dinosaurios en WebP y los SVG de accidentes y radares. Las fotos de las cinco fichas destacadas se exportaron directamente de las capas del prototipo. La de Farmacia del Pueblo está rotulada como referencia.

## Cuenta, reseñas y recompensas

- Se integraron las fichas con reseñas, votos de utilidad y la distribución de calificaciones. La segunda valoración positiva de otra cuenta acredita 100 puntos una sola vez por reseña. Chart.js muestra el gráfico y se conserva una tabla con los valores.
- La tienda permite canjear y equipar 16 dinosaurios. Los dieciséis tienen sus ilustraciones originales. El explorador está disponible desde el inicio. El diálogo de confirmación muestra el dinosaurio y el precio antes de descontar el saldo. El perfil muestra el avatar equipado y los puntos.
- Cuenta y Configuración permiten activar uno de cuatro modos de color. El modo se aplica a toda la interfaz, incluidos encabezados, tarjetas, formularios y gráficos, además de cartografía, marcadores y señales de Tránsito. Las fotos de los comercios conservan sus colores originales. Los modos se guardan en la cuenta o en el navegador para visitantes. Configuración también guarda el estilo del mapa, la muestra de usuarios, los avisos al abrir Tránsito, los marcadores de lugares, el modo de viaje, las unidades de distancia y el tamaño del texto.
- La cartografía vectorial de OpenFreeMap se pintó con la paleta de DinoMaps. Conserva el zoom de Leaflet y tiene respaldo de teselas si falla la carga inicial. La opción Claro usa el estilo Positron sin modificaciones. El selector del mapa alterna Estándar, Híbrido y Satélite sin cambiar el zoom ni los marcadores. Las dos últimas vistas usan Sentinel-2 2023 de EOxCloudless y Híbrido añade etiquetas y calles del overlay EOX. La resolución nativa llega al nivel 14, y la selección queda en el navegador. El uso de este mosaico público es no comercial, con atribución visible.
- Usuarios cercanos funciona con consentimiento y ubicación redondeada, se actualiza cada 15 segundos y caduca en tres minutos. El endpoint no devuelve nombres ni identificadores. Cerrar sesión o desactivar el botón retira la presencia.
- El trazado opcional usa Geoapify desde Laravel con `GEOAPIFY_API_KEY` y muestra avisos propios hasta 80 m del recorrido aproximado. Sin clave se puede usar Cómo llegar en Google Maps. La calle puede tener condiciones no informadas.

## Verificación reciente

SQLite en memoria: migración y seed completos, render de Cuenta, Configuración, tienda, colección, ficha con gráfico y mapa. Se verificaron individualmente el premio único, canje con saldo y posesión, equipamiento y modos de color. La prueba de recorrido usó una respuesta simulada de Geoapify.

DeepSeek reportó **DEMO OK** el 25 de noviembre de 2026 en Docker con MariaDB y Chrome. Comprobó el inicio de sesión válido e incorrecto, la publicación de un aviso de accidente, la ausencia de autoconfirmación en la interfaz, el premio único de 100 puntos en la segunda confirmación y el saldo sin cambios en la tercera. Verificó el canje y equipamiento de Dino cafetero, y la publicación de una reseña con actualización de la tabla y el gráfico. El ensayo usó el entorno local y no modificó el código.

Prueba en navegador reportada por DeepSeek el 25 de noviembre de 2026. Con Docker activo, la vista inicial cargó siete marcadores. La secuencia Estándar → Satélite → Híbrido → Estándar conservó etiquetas, posiciones y zoom 13. Se observaron las solicitudes a EOX y la restauración del mapa vectorial. Queda comprobar otros niveles de zoom, la persistencia, el error de red y las páginas con capas adicionales. Esta prueba cubre la vista inicial del mapa.


## Requisitos de la entrega final

- Hay dos roles autenticados. El registro crea usuarios comunes. El comando `dinomaps:administrador` habilita una cuenta existente y el permiso `gestionar-catalogo` se comprueba en las rutas y en Cuenta.
- El catálogo tiene alta, consulta, modificación y baja lógica. El administrador archiva el lugar para retirarlo del mapa y del catálogo sin borrar reseñas. El seeder preserva los lugares ya editados o archivados.
- El canje asocia usuarios y dinosaurios mediante `belongsToMany` y `attach` sobre `dinosaur_user`. El saldo y la asociación se actualizan juntos en una transacción.
- La [guía de entrega y defensa](DEFENSA.md) incluye permisos, relaciones Eloquent, AJAX, librerías y un orden para ensayar la demo.
- Validación de esta revisión: 9 pruebas de Laravel en SQLite, con 187 aserciones. Chrome mostró el listado, alta y edición a 360 px sin desborde y permitió abrir la confirmación de archivo. Queda aplicar la migración y ensayar con MariaDB.

## Pendientes

Verificar el acceso a la demo compartida desde otro dispositivo si se usa en la entrega. Extender las fotos y completar más datos geográficos del catálogo y las opciones de Configuración restantes. Tráfico de TomTom, mapas de terreno, traducciones, notificaciones y despliegue siguen pendientes. Confirmar claves y cobertura antes de hacer una prueba con proveedores externos.
