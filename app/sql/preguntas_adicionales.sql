-- Preguntas adicionales (12 por categoría, 72 en total).
-- No usa ids fijos: cada pregunta toma el id que le asigna la base.

SET NAMES utf8mb4;

-- ===== Ciencia =====

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Cuál es el planeta más grande del sistema solar?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Neptuno', 0), (@p, 'Tierra', 0), (@p, 'Saturno', 0), (@p, 'Júpiter', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Cuántos huesos tiene el cuerpo humano adulto?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '206', 1), (@p, '300', 0), (@p, '250', 0), (@p, '180', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Qué órgano bombea la sangre por el cuerpo?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'El corazón', 1), (@p, 'Los pulmones', 0), (@p, 'El hígado', 0), (@p, 'El riñón', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Cuál es el símbolo químico del oro?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Go', 0), (@p, 'Or', 0), (@p, 'Au', 1), (@p, 'Ag', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Cuál es el gas más abundante en la atmósfera terrestre?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Dióxido de carbono', 0), (@p, 'Hidrógeno', 0), (@p, 'Oxígeno', 0), (@p, 'Nitrógeno', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿A cuántos grados Celsius hierve el agua al nivel del mar?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '120', 0), (@p, '90', 0), (@p, '100', 1), (@p, '150', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Qué planeta es conocido como el planeta rojo?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Júpiter', 0), (@p, 'Venus', 0), (@p, 'Marte', 1), (@p, 'Mercurio', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Cómo se llama la fuerza que nos mantiene pegados a la Tierra?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Gravedad', 1), (@p, 'Inercia', 0), (@p, 'Magnetismo', 0), (@p, 'Fricción', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Qué científico formuló la teoría de la relatividad?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Isaac Newton', 0), (@p, 'Albert Einstein', 1), (@p, 'Galileo Galilei', 0), (@p, 'Nikola Tesla', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Cuál es el hueso más largo del cuerpo humano?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'El peroné', 0), (@p, 'El húmero', 0), (@p, 'El fémur', 1), (@p, 'La tibia', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Qué tipo de animal es la ballena?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Un anfibio', 0), (@p, 'Un mamífero', 1), (@p, 'Un pez', 0), (@p, 'Un reptil', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (1, '¿Cuál es la unidad básica de los seres vivos?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'La molécula', 0), (@p, 'El átomo', 0), (@p, 'La célula', 1), (@p, 'El tejido', 0);

-- ===== Historia =====

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿En qué año comenzó la Primera Guerra Mundial?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '1914', 1), (@p, '1905', 0), (@p, '1918', 0), (@p, '1939', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿En qué año terminó la Segunda Guerra Mundial?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '1941', 0), (@p, '1950', 0), (@p, '1939', 0), (@p, '1945', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Quién cruzó los Andes al frente del Ejército de los Andes?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Juan Manuel de Rosas', 0), (@p, 'Simón Bolívar', 0), (@p, 'Manuel Belgrano', 0), (@p, 'José de San Martín', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Qué civilización construyó las pirámides de Giza?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Los antiguos egipcios', 1), (@p, 'Los mayas', 0), (@p, 'Los romanos', 0), (@p, 'Los aztecas', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Quién creó la bandera argentina?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Manuel Belgrano', 1), (@p, 'José de San Martín', 0), (@p, 'Bernardino Rivadavia', 0), (@p, 'Domingo Sarmiento', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿En qué año cayó el Muro de Berlín?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '1961', 0), (@p, '1979', 0), (@p, '1991', 0), (@p, '1989', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Qué imperio tenía su capital en Tenochtitlan?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'El romano', 0), (@p, 'El maya', 0), (@p, 'El azteca', 1), (@p, 'El inca', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Qué día se conmemora la Revolución de Mayo en Argentina?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '20 de junio', 0), (@p, '25 de mayo', 1), (@p, '9 de julio', 0), (@p, '17 de agosto', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Quién fue el primer hombre en pisar la Luna?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Buzz Aldrin', 0), (@p, 'Yuri Gagarin', 0), (@p, 'Neil Armstrong', 1), (@p, 'John Glenn', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿En qué año se produjo la Revolución Francesa?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '1789', 1), (@p, '1776', 0), (@p, '1804', 0), (@p, '1815', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Cómo se llamaba el barco que llevó a los peregrinos a América del Norte en 1620?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'El Mayflower', 1), (@p, 'La Santa María', 0), (@p, 'La Pinta', 0), (@p, 'El Titanic', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (2, '¿Qué reina dio nombre a la era victoriana?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Victoria', 1), (@p, 'Isabel I', 0), (@p, 'María Estuardo', 0), (@p, 'Ana', 0);

-- ===== Geografía =====

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es la montaña más alta de América?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Fitz Roy', 0), (@p, 'Aconcagua', 1), (@p, 'Ojos del Salado', 0), (@p, 'Cerro Torre', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es el océano más grande del mundo?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Ártico', 0), (@p, 'Pacífico', 1), (@p, 'Atlántico', 0), (@p, 'Índico', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es el país más grande del mundo por superficie?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'China', 0), (@p, 'Canadá', 0), (@p, 'Estados Unidos', 0), (@p, 'Rusia', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es la capital de Australia?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Melbourne', 0), (@p, 'Canberra', 1), (@p, 'Sídney', 0), (@p, 'Perth', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿En qué país se encuentra Machu Picchu?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Perú', 1), (@p, 'Bolivia', 0), (@p, 'Chile', 0), (@p, 'Ecuador', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Qué río atraviesa la ciudad de París?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'El Sena', 1), (@p, 'El Támesis', 0), (@p, 'El Danubio', 0), (@p, 'El Rin', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es la capital de Brasil?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Río de Janeiro', 0), (@p, 'Brasilia', 1), (@p, 'São Paulo', 0), (@p, 'Salvador', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es el país con más habitantes de Sudamérica?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Brasil', 1), (@p, 'Perú', 0), (@p, 'Argentina', 0), (@p, 'Colombia', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Qué cordillera recorre Sudamérica de norte a sur?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Las Rocosas', 0), (@p, 'El Himalaya', 0), (@p, 'Los Alpes', 0), (@p, 'Los Andes', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es la capital de Japón?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Osaka', 0), (@p, 'Kioto', 0), (@p, 'Tokio', 1), (@p, 'Seúl', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿En qué provincia argentina están las Cataratas del Iguazú?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Chaco', 0), (@p, 'Corrientes', 0), (@p, 'Misiones', 1), (@p, 'Formosa', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (3, '¿Cuál es el continente más pequeño?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Antártida', 0), (@p, 'Oceanía', 1), (@p, 'Europa', 0), (@p, 'África', 0);

-- ===== Arte =====

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Quién pintó La noche estrellada?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Vincent van Gogh', 1), (@p, 'Salvador Dalí', 0), (@p, 'Pablo Picasso', 0), (@p, 'Paul Cézanne', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Quién pintó el techo de la Capilla Sixtina?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Rafael', 0), (@p, 'Miguel Ángel', 1), (@p, 'Donatello', 0), (@p, 'Leonardo da Vinci', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Qué instrumento tiene teclas blancas y negras?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'La flauta', 0), (@p, 'El violín', 0), (@p, 'La batería', 0), (@p, 'El piano', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Cuántas notas musicales básicas hay?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '5', 0), (@p, '12', 0), (@p, '8', 0), (@p, '7', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Qué pintor es famoso por sus relojes derretidos?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Frida Kahlo', 0), (@p, 'Pablo Picasso', 0), (@p, 'Salvador Dalí', 1), (@p, 'Joan Miró', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Quién compuso la Novena Sinfonía?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Ludwig van Beethoven', 1), (@p, 'Wolfgang Amadeus Mozart', 0), (@p, 'Frédéric Chopin', 0), (@p, 'Johann Sebastian Bach', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Quién es el autor del poema Martín Fierro?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Julio Cortázar', 0), (@p, 'Ricardo Güiraldes', 0), (@p, 'José Hernández', 1), (@p, 'Jorge Luis Borges', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Qué artista mexicana es famosa por sus autorretratos?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Yayoi Kusama', 0), (@p, 'Frida Kahlo', 1), (@p, 'Tamara de Lempicka', 0), (@p, 'Mary Cassatt', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Cómo se llama la técnica de pintura que usa pigmentos transparentes diluidos en agua?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Acrílico', 0), (@p, 'Acuarela', 1), (@p, 'Pastel', 0), (@p, 'Óleo', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Quién escribió Cien años de soledad?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Isabel Allende', 0), (@p, 'Mario Vargas Llosa', 0), (@p, 'Gabriel García Márquez', 1), (@p, 'Julio Cortázar', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿En qué museo se exhibe la Mona Lisa?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'La Galería Uffizi', 0), (@p, 'El Museo Británico', 0), (@p, 'El Louvre', 1), (@p, 'El Prado', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (4, '¿Qué banda británica lanzó el álbum Abbey Road?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'The Rolling Stones', 0), (@p, 'The Beatles', 1), (@p, 'Queen', 0), (@p, 'Pink Floyd', 0);

-- ===== Deportes =====

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Qué selección ganó el Mundial de fútbol de 2022?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Croacia', 0), (@p, 'Argentina', 1), (@p, 'Brasil', 0), (@p, 'Francia', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿En qué país se jugó el Mundial de fútbol de 1978?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'España', 0), (@p, 'Uruguay', 0), (@p, 'Brasil', 0), (@p, 'Argentina', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Cuántos jugadores de cada equipo hay en la cancha en un partido de básquet?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '11', 0), (@p, '7', 0), (@p, '6', 0), (@p, '5', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Cuántos jugadores tiene un equipo de vóley en la cancha?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '7', 0), (@p, '5', 0), (@p, '8', 0), (@p, '6', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Cuántos kilómetros tiene aproximadamente una maratón?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '42', 1), (@p, '21', 0), (@p, '100', 0), (@p, '10', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Qué jugador de fútbol es conocido como La Pulga?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Sergio Agüero', 0), (@p, 'Lionel Messi', 1), (@p, 'Diego Maradona', 0), (@p, 'Ángel Di María', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Qué país ganó más Copas del Mundo de fútbol masculino?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Argentina', 0), (@p, 'Brasil', 1), (@p, 'Alemania', 0), (@p, 'Italia', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Cómo se llama el seleccionado argentino de rugby?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Los Cóndores', 0), (@p, 'Los Pumas', 1), (@p, 'Los Tehuelches', 0), (@p, 'Los Leones', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Cuántos anillos tiene el símbolo de los Juegos Olímpicos?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '5', 1), (@p, '4', 0), (@p, '6', 0), (@p, '7', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿En qué deporte se destacó la llamada Generación Dorada argentina?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Fútbol', 0), (@p, 'Básquet', 1), (@p, 'Vóley', 0), (@p, 'Hockey', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿En qué deporte se compite en el Tour de Francia?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Natación', 0), (@p, 'Automovilismo', 0), (@p, 'Atletismo', 0), (@p, 'Ciclismo', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (5, '¿Cuántos sets hay que ganar para llevarse un partido de tenis al mejor de cinco sets?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, '2', 0), (@p, '4', 0), (@p, '5', 0), (@p, '3', 1);

-- ===== Cine =====

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Quién dirigió la película El Padrino?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Martin Scorsese', 0), (@p, 'Quentin Tarantino', 0), (@p, 'Steven Spielberg', 0), (@p, 'Francis Ford Coppola', 1);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Cómo se llama el joven mago protagonista de la saga escrita por J. K. Rowling?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Gandalf', 0), (@p, 'Harry Potter', 1), (@p, 'Merlín', 0), (@p, 'Dumbledore', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿En qué película aparece Woody, el vaquero de juguete?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Cars', 0), (@p, 'Toy Story', 1), (@p, 'Buscando a Nemo', 0), (@p, 'Up', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Qué película argentina ganó el Oscar a Mejor Película Extranjera en 2010?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'El hijo de la novia', 0), (@p, 'El secreto de sus ojos', 1), (@p, 'Nueve reinas', 0), (@p, 'Relatos salvajes', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Cómo se llama la película de Pixar sobre un pez payaso perdido en el océano?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'WALL-E', 0), (@p, 'Ratatouille', 0), (@p, 'Buscando a Nemo', 1), (@p, 'Moana', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Qué actor interpreta a Jack Sparrow en Piratas del Caribe?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Johnny Depp', 1), (@p, 'Brad Pitt', 0), (@p, 'Orlando Bloom', 0), (@p, 'Tom Cruise', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Qué película animada tiene como protagonista a un ogro verde?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Aladdín', 0), (@p, 'Frozen', 0), (@p, 'Shrek', 1), (@p, 'Mulán', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Qué princesa de Disney canta Libre soy?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Bella', 0), (@p, 'Ana', 0), (@p, 'Elsa', 1), (@p, 'Rapunzel', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Quién dirigió la película Jurassic Park de 1993?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Steven Spielberg', 1), (@p, 'George Lucas', 0), (@p, 'Tim Burton', 0), (@p, 'James Cameron', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Quién lleva el Anillo Único hasta Mordor en El Señor de los Anillos?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Frodo', 1), (@p, 'Aragorn', 0), (@p, 'Gandalf', 0), (@p, 'Legolas', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Qué actor interpretó a Iron Man en las películas de Marvel?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Chris Hemsworth', 0), (@p, 'Chris Evans', 0), (@p, 'Robert Downey Jr.', 1), (@p, 'Mark Ruffalo', 0);

INSERT INTO preguntas (categoria_id, texto) VALUES (6, '¿Cómo se llama el niño protagonista de Mi pobre angelito?');
SET @p = LAST_INSERT_ID();
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES (@p, 'Peter', 0), (@p, 'Kevin', 1), (@p, 'Harry', 0), (@p, 'Charlie', 0);

