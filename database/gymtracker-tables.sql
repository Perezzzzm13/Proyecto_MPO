DROP DATABASE IF EXISTS gymtracker;
CREATE DATABASE gymtracker;
USE gymtracker;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(30) NOT NULL,
    apellidos VARCHAR(40),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rutinas (
    id_rutina INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE ejercicios (
    id_ejercicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    grupo_muscular VARCHAR(40),
    descripcion VARCHAR(255)
);

CREATE TABLE rutina_ejercicios (
    id_rutina INT NOT NULL,
    id_ejercicio INT NOT NULL,
    orden INT NOT NULL,
    PRIMARY KEY (id_rutina, id_ejercicio),
    FOREIGN KEY (id_rutina) REFERENCES rutinas(id_rutina)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (id_ejercicio) REFERENCES ejercicios(id_ejercicio)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE sesiones (
    id_sesion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_rutina INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    observaciones VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (id_rutina) REFERENCES rutinas(id_rutina)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE sesion_ejercicios (
    id_sesion_ejercicio INT AUTO_INCREMENT PRIMARY KEY,
    id_sesion INT NOT NULL,
    id_ejercicio INT NOT NULL,
    numero_serie INT NOT NULL,
    repeticiones INT,
    peso DECIMAL(6,2),
    FOREIGN KEY (id_sesion) REFERENCES sesiones(id_sesion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (id_ejercicio) REFERENCES ejercicios(id_ejercicio)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);