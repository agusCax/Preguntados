CREATE DATABASE IF NOT EXISTS preguntados CHARACTER SET utf8mb4;
USE preguntados;

CREATE TABLE usuarios (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
                          password VARCHAR(32) NOT NULL,
                          nombre_1 VARCHAR(100) NOT NULL,
                          nombre_2 VARCHAR(100) NULL,
                          apellido_1 VARCHAR(100) NOT NULL,
                          apellido_2 VARCHAR(100) NULL,
                          anio_nacimiento SMALLINT UNSIGNED NOT NULL,
                          sexo VARCHAR(30) NOT NULL,
                          pais VARCHAR(100) NOT NULL,
                          ciudad VARCHAR(100) NOT NULL,
                          latitud DECIMAL(10, 8) NOT NULL,
                          longitud DECIMAL(11, 8) NOT NULL,
                          email VARCHAR(254) NOT NULL,
                          estado BOOLEAN DEFAULT FALSE,
                          token_validacion VARCHAR(254),
                          foto_perfil VARCHAR(255) NULL,
                          bio VARCHAR(255) NULL,
                          CONSTRAINT uq_usuarios_email UNIQUE (email)
);

CREATE TABLE IF NOT EXISTS categorias (
                                          id INT AUTO_INCREMENT PRIMARY KEY,
                                          nombre VARCHAR(50) NOT NULL UNIQUE,
    color CHAR(7) NOT NULL,          -- formato #RRGGBB (pinta la pantalla de la pregunta)
    icono VARCHAR(10) NULL           -- emoji
    ) DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS preguntas (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         categoria_id INT NOT NULL,
                                         texto VARCHAR(500) NOT NULL,
    -- activa: se puede jugar | pendiente: sugerida por un usuario, falta aprobar
    -- reportada: un usuario la marcó como inválida | inactiva: dada de baja
    estado ENUM('activa', 'pendiente', 'reportada', 'inactiva') NOT NULL DEFAULT 'activa',
    CONSTRAINT fk_preguntas_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id)
    ) DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS respuestas (
                                          id INT AUTO_INCREMENT PRIMARY KEY,
                                          pregunta_id INT NOT NULL,
                                          texto VARCHAR(255) NOT NULL,
    es_correcta TINYINT(1) NOT NULL DEFAULT 0,   -- 1 = la correcta (una por pregunta)
    CONSTRAINT fk_respuestas_pregunta FOREIGN KEY (pregunta_id) REFERENCES preguntas (id) ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS partidas (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        usuario_id INT NOT NULL,
                                        estado ENUM('en_curso', 'terminada') NOT NULL DEFAULT 'en_curso',
    puntaje INT NOT NULL DEFAULT 0,
    fecha_inicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME NULL,
    CONSTRAINT fk_partidas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ) DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS partida_preguntas (
                                                 id INT AUTO_INCREMENT PRIMARY KEY,
                                                 partida_id INT NOT NULL,
                                                 pregunta_id INT NOT NULL,
                                                 mostrada_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- desde acá corre el tiempo
                                                 respuesta_id INT NULL,        -- opción elegida (NULL si todavía no respondió o se acabó el tiempo)
                                                 fue_correcta TINYINT(1) NULL, -- NULL hasta que responde
    respondida_en DATETIME NULL,
    CONSTRAINT fk_pp_partida FOREIGN KEY (partida_id) REFERENCES partidas (id) ON DELETE CASCADE,
    CONSTRAINT fk_pp_pregunta FOREIGN KEY (pregunta_id) REFERENCES preguntas (id),
    CONSTRAINT fk_pp_respuesta FOREIGN KEY (respuesta_id) REFERENCES respuestas (id),
    -- una pregunta no se repite dentro de la misma partida
    UNIQUE KEY uq_partida_pregunta (partida_id, pregunta_id)
    ) DEFAULT CHARSET = utf8mb4;


INSERT INTO categorias (id, nombre, color, icono) VALUES
                                                      (1, 'Ciencia', '#10b981', '🔬'),
                                                      (2, 'Historia', '#f59e0b', '🏛️'),
                                                      (3, 'Geografía', '#0ea5e9', '🌎'),
                                                      (4, 'Arte', '#ec4899', '🎨'),
                                                      (5, 'Deportes', '#ef4444', '⚽'),
                                                      (6, 'Cine', '#8b5cf6', '🎬');

INSERT INTO preguntas (id, categoria_id, texto) VALUES
                                                    (1, 1, '¿Cuál es el planeta más cercano al Sol?'),
                                                    (2, 1, '¿Cuál es la fórmula química del agua?'),
                                                    (3, 1, '¿Qué gas absorben las plantas para hacer la fotosíntesis?'),
                                                    (4, 2, '¿En qué año llegó Cristóbal Colón a América?'),
                                                    (5, 2, '¿En qué año se declaró la independencia de la Argentina?'),
                                                    (6, 2, '¿Quién fue el primer presidente de los Estados Unidos?'),
                                                    (7, 3, '¿Cuál es la capital de Francia?'),
                                                    (8, 3, '¿Cuál es el río más caudaloso del mundo?'),
                                                    (9, 3, '¿En qué continente se encuentra Egipto?'),
                                                    (10, 4, '¿Quién pintó la Mona Lisa (La Gioconda)?'),
                                                    (11, 4, '¿Cuántas cuerdas tiene una guitarra clásica?'),
                                                    (12, 4, '¿Quién escribió Don Quijote de la Mancha?'),
                                                    (13, 5, '¿Cuántos jugadores tiene un equipo de fútbol en la cancha?'),
                                                    (14, 5, '¿Cada cuántos años se juega el Mundial de fútbol?'),
                                                    (15, 5, '¿En qué deporte los puntos se cuentan en games y sets?'),
                                                    (16, 6, '¿Quién dirigió la película Titanic (1997)?'),
                                                    (17, 6, 'En Star Wars, ¿cómo se llama el villano del casco negro y la respiración pesada?'),
                                                    (18, 6, '¿En qué película aparece el personaje Simba?');

INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
                                                             (1, 'Venus', 0), (1, 'Mercurio', 1), (1, 'Marte', 0), (1, 'Tierra', 0),
                                                             (2, 'O2', 0), (2, 'H2O', 1), (2, 'CO2', 0), (2, 'HO', 0),
                                                             (3, 'Oxígeno', 0), (3, 'Nitrógeno', 0), (3, 'Dióxido de carbono', 1), (3, 'Helio', 0),
                                                             (4, '1492', 1), (4, '1500', 0), (4, '1453', 0), (4, '1810', 0),
                                                             (5, '1810', 0), (5, '1816', 1), (5, '1853', 0), (5, '1776', 0),
                                                             (6, 'Abraham Lincoln', 0), (6, 'Thomas Jefferson', 0), (6, 'George Washington', 1), (6, 'John Adams', 0),
                                                             (7, 'Madrid', 0), (7, 'París', 1), (7, 'Roma', 0), (7, 'Berlín', 0),
                                                             (8, 'Nilo', 0), (8, 'Amazonas', 1), (8, 'Paraná', 0), (8, 'Misisipi', 0),
                                                             (9, 'Asia', 0), (9, 'Europa', 0), (9, 'África', 1), (9, 'Oceanía', 0),
                                                             (10, 'Pablo Picasso', 0), (10, 'Leonardo da Vinci', 1), (10, 'Vincent van Gogh', 0), (10, 'Miguel Ángel', 0),
                                                             (11, '4', 0), (11, '5', 0), (11, '6', 1), (11, '8', 0),
                                                             (12, 'Miguel de Cervantes', 1), (12, 'Gabriel García Márquez', 0), (12, 'Jorge Luis Borges', 0), (12, 'Lope de Vega', 0),
                                                             (13, '9', 0), (13, '10', 0), (13, '11', 1), (13, '12', 0),
                                                             (14, '2', 0), (14, '4', 1), (14, '5', 0), (14, '6', 0),
                                                             (15, 'Básquet', 0), (15, 'Tenis', 1), (15, 'Rugby', 0), (15, 'Natación', 0),
                                                             (16, 'Steven Spielberg', 0), (16, 'James Cameron', 1), (16, 'Ridley Scott', 0), (16, 'Christopher Nolan', 0),
                                                             (17, 'Darth Vader', 1), (17, 'Luke Skywalker', 0), (17, 'Han Solo', 0), (17, 'Obi-Wan Kenobi', 0),
                                                             (18, 'Aladdín', 0), (18, 'El Rey León', 1), (18, 'Bambi', 0), (18, 'Tarzán', 0);
