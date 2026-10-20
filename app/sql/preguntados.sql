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
    foto_perfil VARCHAR(255) NULL,
    CONSTRAINT uq_usuarios_email UNIQUE (email)
);
