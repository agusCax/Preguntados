# Cerebrito

Aplicación web de preguntas y respuestas estilo *Preguntados*, desarrollada en **PHP** con arquitectura **MVC propia** (sin framework). Los usuarios se registran, validan su cuenta por mail, juegan partidas cronometradas con sonidos y animaciones, compiten en un ranking y tienen un perfil público que se puede compartir mediante un código QR. Además, hay un panel para **editores** (moderación de preguntas) y otro para **administradores** (estadísticas y gestión de usuarios).

---

## 📑 Índice

1. [Librerías y servicios externos](#-librerías-y-servicios-externos)
2. [Arquitectura](#-arquitectura)
3. [Estructura de carpetas](#-estructura-de-carpetas)
4. [Base de datos](#-base-de-datos)
5. [Roles y permisos](#-roles-y-permisos)
6. [Rutas disponibles](#-rutas-disponibles)
7. [Funcionamiento del juego](#-funcionamiento-del-juego)
8. [Panel del administrador](#-panel-del-administrador)
9. [Deploy](#-deploy)
10. [Equipo](#-equipo)

---

## 📦 Librerías y servicios externos

### Librerías PHP (carpeta `vendor/`, incluidas en el repo — no se usa Composer)

Cada librería se usa a través de una clase de `helper/` que la envuelve, así el resto del código no depende de cómo funciona por dentro.

| Librería | Para qué se usa | Dónde se usa |
|---|---|---|
| [**Mustache.php**](https://github.com/bobthecow/mustache.php) | Motor de plantillas para renderizar las vistas `.mustache` | `helper/MustacheRender.php` |
| [**PHPMailer**](https://github.com/PHPMailer/PHPMailer) | Envío de mails por SMTP (mail de activación de cuenta) | `helper/Mailer.php` |
| [**PHP QR Code**](http://phpqrcode.sourceforge.net/) | Generación de imágenes QR en formato PNG | `helper/QrLib.php` |
| [**CpChart**](https://github.com/szymach/c-pchart) (3.x) | Dibujo de gráficos como imagen PNG desde el servidor (requiere la extensión GD de PHP) | `helper/CpChartLib.php` |

### Librerías y servicios del lado del cliente (por CDN)

| Recurso | Para qué se usa |
|---|---|
| [**Chart.js**](https://www.chartjs.org/) 4.4.1 | Gráficos interactivos del panel del administrador |
| [**Leaflet**](https://leafletjs.com/) 1.9.4 | Mapa interactivo para elegir/mostrar la ubicación del usuario |
| [**OpenStreetMap**](https://www.openstreetmap.org/) (tiles) | Capa de mapa base de Leaflet |
| [**Nominatim**](https://nominatim.org/) (OSM) | Geocodificación inversa: obtener país y ciudad a partir de latitud/longitud |
| [**Google Fonts**](https://fonts.google.com/) | Tipografías (Fredoka, Nunito, Outfit) |

### Servicios

| Servicio | Uso |
|---|---|
| **Servidor SMTP** (configurado en `config.ini`, sección `[mailer]`) | Salida de los mails de activación |
| **InfinityFree** | Hosting de producción (PHP + MySQL) |
| **GitHub Actions** | Deploy automático a producción |

---

## 🏗 Arquitectura

El proyecto sigue el patrón **MVC** con **inyección de dependencias manual**:

```
Request ──► .htaccess ──► index.php ──► Router ──► Controller ──► Model ──► MyDatabase (PDO)
                                                       │
                                                       └──► MustacheRender ──► Vista (.mustache)
```

**Piezas principales:**

| Pieza | Archivo | Responsabilidad |
|---|---|---|
| Front controller | `index.php` | Punto de entrada único. Inicia la sesión, define `BASE_URL`/`APP_URL` y despacha la petición. |
| Configuration | `app/Configuration.php` | "Contenedor de dependencias": construye controladores, modelos, base de datos, render y mailer. |
| Router | `helper/Router.php` | Convierte `controller` + `method` en una llamada a `get{Nombre}Controller()` de `Configuration`. Si no existe, usa `inicio/show`. |
| Autoloader | `helper/Autoloader.php` | Carga automática de clases por namespace y de las clases globales de `helper/`. |
| MyDatabase | `helper/MyDatabase.php` | Wrapper de PDO con consultas preparadas (`queryOne`, `queryAll`, `execute`). |
| MustacheRender | `helper/MustacheRender.php` | Renderiza vistas, con o sin `header.php` / `footer.php`. |
| Mailer | `helper/Mailer.php` | Wrapper de PHPMailer. |
| QrLib | `helper/QrLib.php` | Wrapper de PHP QR Code. |
| CpChartLib | `helper/CpChartLib.php` | Wrapper de CpChart (gráficos de barras en PNG). |
| Log | `helper/Log.php` | Escribe en `log/AAAA-MM-DD.log`. |
| Request / Redirect | `helper/` | Lectura de `$_GET`/`$_POST` y redirecciones. |

**Criterios generales del proyecto:**

- Toda la lógica y las validaciones están en el servidor (PHP). El JavaScript se usa solo para mejorar la experiencia (temporizador, gráficos, mapa).
- Las vistas son únicamente archivos `.mustache`, con sus estilos y scripts dentro de cada vista.
- Los nombres de archivos de las vistas van en minúscula/camelCase respetando mayúsculas, porque el hosting de producción distingue mayúsculas de minúsculas.

---

## 📁 Estructura de carpetas

```
proyectoWeb/
├── .github/workflows/      # Pipeline de deploy (GitHub Actions)
├── app/
│   ├── Configuration.php   # Contenedor de dependencias
│   ├── controller/         # Administrador, Editor, Inicio, Login, Perfil, Pregunta, Ranking, Registro
│   ├── enum/               # Rol, EstadoPregunta, EstadoReporte
│   ├── model/              # Modelos (acceso a datos)
│   ├── sql/                # Script de la base de datos (preguntados.sql)
│   └── view/               # Vistas .mustache + header.php / footer.php
│       ├── administrador/  # Panel y gestión de usuarios
│       └── editor/         # Moderación de sugerencias y reportes
├── config/
│   └── config.ini          # Credenciales y configuración (NO se sube a Git)
├── helper/                 # Clases de infraestructura (Router, DB, Mailer, QR, gráficos, etc.)
├── log/                    # Logs diarios (NO se suben a Git)
├── public/
│   ├── img/                # Imágenes estáticas
│   ├── sounds/             # Sonidos de respuesta correcta / incorrecta
│   ├── qr/                 # QR generados (se crea automáticamente)
│   └── uploads/perfiles/   # Fotos de perfil subidas (NO se suben a Git)
├── vendor/
│   ├── cpchart/
│   ├── mustache/
│   ├── phpmailer/
│   └── phpqrcode/
├── .htaccess               # Reescritura de URLs y reglas de seguridad
├── .gitignore
└── index.php               # Front controller
```

---

## 🗄 Base de datos

Nombre de la base: `preguntados`. El script completo (estructura + datos de ejemplo) está en [`app/sql/preguntados.sql`](app/sql/preguntados.sql).

| Tabla | Descripción |
|---|---|
| `roles` | Roles del sistema: jugador (1), editor (2) y administrador (3). |
| `usuarios` | Datos personales, credenciales, rol, ubicación (lat/long), estado de activación, token de validación, foto, bio y `fecha_registro`. |
| `categorias` | Nombre, color (`#RRGGBB`) e ícono (emoji). |
| `preguntas` | Texto, categoría, `fecha_creacion` y estado: `activa`, `pendiente`, `reportada`, `inactiva`. |
| `respuestas` | Opciones de cada pregunta; `es_correcta` marca la correcta. |
| `partidas` | Partida de un usuario: estado (`en_curso`/`terminada`), puntaje y fechas. |
| `partida_preguntas` | Preguntas mostradas en una partida, respuesta elegida, si fue correcta y tiempos. Clave única `(partida_id, pregunta_id)` para no repetir. |
| `reportes` | Reportes sobre preguntas que gestiona el editor: `pendiente`, `aceptado` o `rechazado`. |

---

## 🔐 Roles y permisos

El rol de cada usuario se guarda en `usuarios.rol_id` y se representa en el código con el enum `app\enum\Rol`. Al iniciar sesión se guarda en `$_SESSION['rol']`, y cada controlador protegido lo verifica **en el servidor**.

| Rol | Qué puede hacer |
|---|---|
| **Jugador** | Jugar partidas, ver el ranking, editar su perfil y compartir su QR. |
| **Editor** | Moderar las preguntas sugeridas y los reportes (aceptar, rechazar, modificar o inactivar preguntas). |
| **Administrador** | Ver el panel de estadísticas con filtro por período, imprimir los reportes y cambiar el rol de los demás usuarios. |

Desde `/inicio/show` cada usuario es dirigido a la pantalla que le corresponde según su rol.

---

## 🌐 Rutas disponibles

Las URLs siguen el formato `/{controller}/{method}` (Apache las reescribe a `index.php?controller=...&method=...`). Sin parámetros se carga `inicio/show`.

| Ruta | Descripción |
|---|---|
| `/inicio/show` | Pantalla de inicio según el rol |
| `/login/show` · `/login/login` · `/login/logout` | Formulario, autenticación y cierre de sesión |
| `/registro/show` · `/registro/registrar` · `/registro/validarCuenta` | Formulario, alta de usuario y activación por token |
| `/pregunta/show` · `/pregunta/responder` | Mostrar la pregunta de la partida y registrar la respuesta |
| `/ranking/show` | Ranking de jugadores por puntaje máximo |
| `/perfil/show` · `/perfil/editar` · `/perfil/actualizar` · `/perfil/subirFoto` | Ver, editar y guardar el perfil propio, y subir la foto |
| `/perfil/compartir` | Muestra el código QR del perfil |
| `/perfil/showUsuario?usuario_id=N` | Perfil público de un usuario |
| `/editor/show` | Inicio del editor |
| `/editor/listarSugerencias` · `/editor/detalleSugerencia` · `/editor/procesarSugerencia` | Moderación de preguntas sugeridas |
| `/editor/listarReportes` · `/editor/detalleReporte` · `/editor/procesarReporte` | Moderación de reportes de preguntas |
| `/editor/modificarPregunta` · `/editor/inactivarPregunta` | Edición e inactivación de preguntas |
| `/administrador/show` | Panel de estadísticas (solo administrador) |
| `/administrador/datos?periodo=mes` | Datos del panel en JSON (`dia`, `semana`, `mes`, `anio`) |
| `/administrador/graficoPais?periodo=mes` | Gráfico de usuarios por país como imagen PNG (CpChart) |
| `/administrador/usuarios` · `/administrador/cambiarRol` | Lista de usuarios y cambio de rol |

---

## 🎮 Funcionamiento del juego

- Las preguntas son de opción múltiple (A, B, C, D) y de categorías variadas: Ciencia, Historia, Geografía, Arte, Deportes y Cine.
- Cada pregunta tiene **30 segundos** para responderse. Si se acaba el tiempo, la partida termina.
- La partida continúa mientras se responda bien. Termina cuando se responde mal, se acaba el tiempo o no quedan preguntas disponibles.
- Un usuario **no ve preguntas que ya vio**, salvo que ya no queden más.

**Puntaje**

| Situación | Puntos |
|---|---|
| Respuesta correcta | +1 |
| 5 respuestas correctas seguidas (bono) | +5 extra (se reinicia la racha) |
| Respuesta incorrecta en pregunta **fácil** | −3 |
| Respuesta incorrecta en pregunta **regular** | −2 |
| Respuesta incorrecta en pregunta **difícil** | −1 |

El puntaje nunca baja de 0. La pantalla de la pregunta muestra una barra con la racha hacia el bono, y la pantalla de resultado explica la penalización aplicada.

**Dificultad**

- **De la pregunta:** si se responde bien el 70% de las veces o más es *fácil*; si es el 30% o menos es *difícil*; en el medio es *regular*. Una pregunta nueva empieza en 50%.
- **Del jugador:** se calcula con su porcentaje de aciertos histórico. Si acierta el 70% o más se le muestran preguntas difíciles; si acierta el 30% o menos, fáciles; en el medio, regulares.

**Sonidos y animaciones**

- Sonido distinto para respuesta correcta e incorrecta (`public/sounds/`).
- Banner animado al sumar puntos (+1 / bono), mensaje animado de penalización y animación de entrada en el ranking. Las animaciones respetan `prefers-reduced-motion`.

---

## 📊 Panel del administrador

En `/administrador/show` se muestran, filtrando por **día, semana, mes o año**:

- Cantidad de jugadores, partidas, preguntas activas, preguntas creadas y usuarios nuevos.
- Usuarios por **país**, por **sexo** y por **grupo de edad** (menores, medio, jubilados).
- Porcentaje de **aciertos por usuario** (top 10).

Los gráficos se dibujan en el navegador con **Chart.js** (datos en JSON desde `/administrador/datos`). La librería **CpChart** genera la misma información como imagen PNG desde el servidor (`/administrador/graficoPais`). El panel se puede imprimir desde el botón "Imprimir".

Desde "Gestionar usuarios" (`/administrador/usuarios`) el administrador puede cambiar el rol de cualquier usuario. El servidor valida que el rol exista y no permite que el administrador modifique su propio rol.

---

## 🚀 Deploy

El deploy es automático con **GitHub Actions** (`.github/workflows/github-actions-demo.yml`):

- **Disparador:** cada `push` a la rama `main`.
- **Proceso:** sube los archivos por FTP con la acción [`FTP-Deploy-Action`](https://github.com/SamKirkland/FTP-Deploy-Action) hacia `/htdocs/proyectoWeb/` en InfinityFree. Solo sube lo que cambió desde el último deploy.
- **Excluye:** `.git`, `.github/` y `config/config.ini`.
- **Secrets requeridos** (en *Settings → Secrets and variables → Actions*): `FTP_HOST`, `FTP_USER`, `FTP_PASS`.

Como `config.ini` no se sube, **hay que crearlo manualmente en el servidor** con las credenciales de producción. Además, InfinityFree corre en Linux y **distingue mayúsculas de minúsculas** en nombres de archivos y carpetas.

---

## 👥 Equipo

- Emanuel Abel Ruiz Díaz
- Agustina Cáceres Pérez
- Santiago Javier Falconi
- Juan Manuel Pena