-- Baja logica de categorias: correr UNA sola vez en cada base (local e InfinityFree)
ALTER TABLE categorias ADD COLUMN activa TINYINT(1) NOT NULL DEFAULT 1 AFTER icono;
