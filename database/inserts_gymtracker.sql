INSERT INTO usuarios (nombre_usuario, nombre, apellidos, email, password, rol)
VALUES 
('marco', 'Marco', 'Pérez', 'marco@test.com', '1234', 'usuario');

INSERT INTO ejercicios (nombre, grupo_muscular, descripcion)
VALUES
('Press inclinado', 'Pecho', 'Press inclinado en máquina'),
('Jalón al pecho', 'Espalda', 'Polea alta al pecho'),
('Elevaciones laterales', 'Hombro', 'Elevaciones con mancuernas'),
('Curl bíceps', 'Bíceps', 'Curl en máquina'),
('Extensión tríceps', 'Tríceps', 'Extensión en polea');

INSERT INTO rutina_ejercicios (id_rutina, id_ejercicio, orden)
VALUES
(1, 1, 1), -- Press inclinado
(1, 3, 2), -- Elevaciones laterales
(1, 5, 3); -- Tríceps

INSERT INTO rutina_ejercicios (id_rutina, id_ejercicio, orden)
VALUES
(2, 2, 1), -- Jalón
(2, 4, 2); -- Bíceps

INSERT INTO sesiones (id_usuario, id_rutina, observaciones)
VALUES
(1, 1, 'Buen entrenamiento');

-- Press inclinado (ejercicio 1)
INSERT INTO sesion_ejercicios (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
VALUES
(1, 1, 1, 10, 20),
(1, 1, 2, 9, 22.5),
(1, 1, 3, 8, 22.5);

-- Elevaciones laterales (ejercicio 3)
INSERT INTO sesion_ejercicios (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
VALUES
(1, 3, 1, 12, 7.5),
(1, 3, 2, 12, 7.5);

-- Tríceps (ejercicio 5)
INSERT INTO sesion_ejercicios (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
VALUES
(1, 5, 1, 12, 15),
(1, 5, 2, 10, 15);

INSERT INTO rutinas (nombre, descripcion, id_usuario)
VALUES
('Rutina Push', 'Pecho, hombro y tríceps', 1),
('Rutina Pull', 'Espalda y bíceps', 1);

INSERT INTO sesiones (id_usuario, id_rutina, observaciones)
VALUES
(1, 1, 'Buen entrenamiento');


-- Press inclinado (ejercicio 1)
INSERT INTO sesion_ejercicios (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
VALUES
(1, 1, 1, 10, 20),
(1, 1, 2, 9, 22.5),
(1, 1, 3, 8, 22.5);

-- Elevaciones laterales (ejercicio 3)
INSERT INTO sesion_ejercicios (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
VALUES
(1, 3, 1, 12, 7.5),
(1, 3, 2, 12, 7.5);

-- Tríceps (ejercicio 5)
INSERT INTO sesion_ejercicios (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
VALUES
(1, 5, 1, 12, 15),
(1, 5, 2, 10, 15);

-- Rutina Push (id 1)
INSERT INTO rutina_ejercicios (id_rutina, id_ejercicio, orden)
VALUES
(1, 1, 1), -- Press inclinado
(1, 3, 2), -- Elevaciones laterales
(1, 5, 3); -- Tríceps

-- Rutina Pull (id 2)
INSERT INTO rutina_ejercicios (id_rutina, id_ejercicio, orden)
VALUES
(2, 2, 1), -- Jalón
(2, 4, 2); -- Bíceps

SELECT * FROM rutinas;

SELECT * FROM sesiones;

SELECT * FROM usuarios;
SELECT * FROM rutinas;

SELECT e.nombre, re.orden
FROM rutina_ejercicios re
JOIN ejercicios e ON re.id_ejercicio = e.id_ejercicio
WHERE re.id_rutina = 1
ORDER BY re.orden;

SELECT e.nombre, se.numero_serie, se.repeticiones, se.peso
FROM sesion_ejercicios se
JOIN ejercicios e ON se.id_ejercicio = e.id_ejercicio
WHERE se.id_sesion = 1
ORDER BY e.nombre, se.numero_serie;
