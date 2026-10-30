# Cerebrito

Aplicación web de preguntas y respuestas estilo *Preguntados*, desarrollada en **PHP** con arquitectura **MVC propia** (sin framework). Los usuarios se registran, validan su cuenta por mail, juegan partidas cronometradas y tienen un perfil público que se puede compartir mediante un código QR.

---

## 📑 Índice

1. [Librerías y servicios externos](#librerías-y-servicios-externos)
2. [Arquitectura](#arquitectura)
3. [Estructura de carpetas](#estructura-de-carpetas)
4. [Base de datos](#base-de-datos)
5. [Rutas disponibles](#rutas-disponibles)
6. [Deploy](#deploy)
7. [Equipo](#equipo)

---


## 📦 Librerías y servicios externos

### Librerías PHP (carpeta `vendor/`, incluidas en el repo — no se usa Composer)

| Librería | Versión | Para qué se usa | Dónde se usa |
|---|---|---|---|
| [**Mustache.php**](https://github.com/bobthecow/mustache.php) | 2.12.0 | Motor de plantillas para renderizar las vistas `.mustache` | `helper/MustacheRender.php` |
| [**PHPMailer**](https://github.com/PHPMailer/PHPMailer) | 7.1.1 | Envío de mails por SMTP (mail de activación de cuenta) | `helper/Mailer.php` |
| [**PHP QR Code**](http://phpqrcode.sourceforge.net/) | 1.1.4 | Generación de imágenes QR en formato PNG | `helper/QrLib.php` |

### Librerías y servicios del lado del cliente (por CDN)

| Recurso | Versión | Para qué se usa |
|---|---|---|
| [**Leaflet**](https://leafletjs.com/) | 1.9.4 | Mapa interactivo para elegir/mostrar la ubicación del usuario |
| [**OpenStreetMap**](https://www.openstreetmap.org/) (tiles) | — | Capa de mapa base de Leaflet |
| [**Nominatim**](https://nominatim.org/) (OSM) | — | Geocodificación inversa: obtener país y ciudad a partir de latitud/longitud |
| [**Google Fonts**](https://fonts.google.com/) | — | Tipografías (Fredoka, Outfit) |

### Servicios

| Servicio | Uso |
|---|---|
| **Gmail SMTP** (`smtp.gmail.com:587`, STARTTLS) | Servidor de salida para los mails de activación |
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
| Log | `helper/Log.php` | Escribe en `log/AAAA-MM-DD.log`. |
| Request / Redirect | `helper/` | Lectura de `$_GET`/`$_POST` y redirecciones. |

---

## 📁 Estructura de carpetas

```
proyectoWeb/
├── .github/workflows/      # Pipeline de deploy (GitHub Actions)
├── app/
│   ├── Configuration.php   # Contenedor de dependencias
│   ├── controller/         # Controladores (Inicio, Login, Registro, Pregunta, Perfil)
│   ├── model/              # Modelos (acceso a datos)
│   ├── sql/                # Script de la base de datos (preguntados.sql)
│   └── view/               # Vistas .mustache + header.php / footer.php
├── config/
│   └── config.ini          # Credenciales y configuración (NO se sube a Git)
├── helper/                 # Clases de infraestructura (Router, DB, Mailer, etc.)
├── log/                    # Logs diarios (NO se suben a Git)
├── public/
│   ├── img/                # Imágenes estáticas
│   ├── qr/                 # QR generados (se crea automáticamente)
│   └── uploads/perfiles/   # Fotos de perfil subidas (NO se suben a Git)
├── vendor/
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
| `usuarios` | Datos personales, credenciales, ubicación (lat/long), estado de activación, token de validación, foto y bio. |
| `categorias` | Nombre, color (`#RRGGBB`) e ícono (emoji). |
| `preguntas` | Texto, categoría y estado: `activa`, `pendiente`, `reportada`, `inactiva`. |
| `respuestas` | Opciones de cada pregunta; `es_correcta` marca la correcta. |
| `partidas` | Partida de un usuario: estado (`en_curso`/`terminada`), puntaje y fechas. |
| `partida_preguntas` | Preguntas mostradas en una partida, respuesta elegida, si fue correcta y tiempos. Clave única `(partida_id, pregunta_id)` para no repetir. |

```
usuarios ──< partidas ──< partida_preguntas >── preguntas ──< respuestas
                                                    │
                                                categorias
```

El script incluye **6 categorías, 18 preguntas y 72 respuestas** de ejemplo.

---

## 🌐 Rutas disponibles

Las URLs siguen el formato `/{controller}/{method}` (Apache las reescribe a `index.php?controller=...&method=...`). Sin parámetros se carga `inicio/show`.

| Ruta | Descripción |
|---|---|
| `/inicio/show` | Pantalla de inicio |
| `/login/show` · `/login/login` · `/login/logout` | Formulario, autenticación y cierre de sesión |
| `/registro/show` · `/registro/registrar` · `/registro/validarCuenta` | Formulario, alta de usuario y activación por token |
| `/pregunta/show` · `/pregunta/responder` | Mostrar pregunta de la partida y registrar respuesta |
| `/perfil/show` · `/perfil/editar` · `/perfil/actualizar` | Ver, editar y guardar el perfil propio |
| `/perfil/compartir` | Muestra el código QR del perfil |
| `/perfil/showUsuario?usuario_id=N` | Perfil público de un usuario |

---

## 🚀 Deploy

El deploy es automático con **GitHub Actions** (`.github/workflows/github-actions-demo.yml`):

- **Disparador:** cada `push` a la rama `main`.
- **Proceso:** sincroniza los archivos por FTP con `lftp` hacia `/htdocs/proyectoWeb/` en InfinityFree.
- **Excluye:** `.git/`, `.github/` y `config/config.ini`.
- **Secrets requeridos** (en *Settings → Secrets and variables → Actions*): `FTP_HOST`, `FTP_USER`, `FTP_PASS`.

Como `config.ini` no se sube, **hay que crearlo manualmente en el servidor** con las credenciales de producción.

---

##  Funcionamiento del juego

- Responder preguntas de categorías (Ciencia, Historia, Geografía, Arte, Deportes, Cine) aleatorias del tipo ABCD, dentro de una partida de 15 preguntas totales.
- Cada respuesta válida acumula 1 (un) punto. Ante una respuesta incorrecta, la partida se termina, se indica la opción correcta, y se hace el recuento de puntos acumulados. 

##  Reglas de negocio 
- Los usuarios no verán preguntas que ya hayan visto, salvo que ya no haya más preguntas.
- Nivel de dificultad de las preguntas: Si la pregunta se responde bien más del 70% de las veces, es fácil. Si se responde menos del 30% es dificil.
- Nivel de dificulad manejada por el jugador: De acuerdo al porcentaje de preguntas difíciles que responde correctamente.

---

## 👥 Equipo

- Emanuel Abel Ruiz Díaz
- Agustina Cáceres Pérez
- Santiago Javier Falconi
- Juan Manuel Pena
