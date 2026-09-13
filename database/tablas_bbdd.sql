CREATE TABLE usuarios (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) UNIQUE,
    contrasenna VARCHAR(255)
);

CREATE TABLE tareas (
    idTarea INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    nombre VARCHAR(30) NOT NULL,
    descripcion VARCHAR(500),
    estaActiva BOOLEAN DEFAULT 1,
    esPosponible BOOLEAN DEFAULT 1,
    esSemanal BOOLEAN DEFAULT 1,
    esHabito BOOLEAN DEFAULT 0,
    racha INT DEFAULT 0,
    puntosAcumulados INT DEFAULT 0,
    CONSTRAINT fk_idUsuario FOREIGN KEY (idUsuario) REFERENCES usuarios(idUsuario)
);

CREATE TABLE semana(
    idTarea INT PRIMARY KEY,
    mon BOOLEAN,
    tue BOOLEAN,
    wed BOOLEAN,
    thu BOOLEAN,
    fri BOOLEAN,
    sat BOOLEAN,
    sun BOOLEAN,
    CONSTRAINT fk_idTarea_semana FOREIGN KEY (idTarea) REFERENCES tareas(idTarea)
);


CREATE TABLE mes(
    idTarea INT PRIMARY KEY,
    dia1 BOOLEAN,
    dia2 BOOLEAN,
    dia3 BOOLEAN,
    dia4 BOOLEAN,
    dia5 BOOLEAN,
    dia6 BOOLEAN,
    dia7 BOOLEAN,
    dia8 BOOLEAN,
    dia9 BOOLEAN,
    dia10 BOOLEAN,
    dia11 BOOLEAN,
    dia12 BOOLEAN,
    dia13 BOOLEAN,
    dia14 BOOLEAN,
    dia15 BOOLEAN,
    dia16 BOOLEAN,
    dia17 BOOLEAN,
    dia18 BOOLEAN,
    dia19 BOOLEAN,
    dia20 BOOLEAN,
    dia21 BOOLEAN,
    dia22 BOOLEAN,
    dia23 BOOLEAN,
    dia24 BOOLEAN,
    dia25 BOOLEAN,
    dia26 BOOLEAN,
    dia27 BOOLEAN,
    dia28 BOOLEAN,
    dia29 BOOLEAN,
    dia30 BOOLEAN,
    dia31 BOOLEAN,
    CONSTRAINT fk_idTarea_mes FOREIGN KEY (idTarea) REFERENCES tareas(idTarea)
);

CREATE TABLE registros (
	idTarea INT,
    fecha DATE DEFAULT (CURRENT_DATE - INTERVAL 1 DAY), /*se guarda a partir de las 00:00, así que se guarda lo del día anterior*/
    estado char(1),
    puntos INT, /*puntos que sumó al cumplirse*/
    PRIMARY KEY (idTarea, fecha),
    CONSTRAINT fk_idTarea_registros FOREIGN KEY (idTarea) REFERENCES tareas(idTarea)
);

CREATE TABLE registros_temp(
    idTarea INT PRIMARY KEY,
    estaCumplida BOOLEAN DEFAULT 0,
    CONSTRAINT fk_idTarea_registros_temp FOREIGN KEY (idTarea) REFERENCES tareas(idTarea)
);

/* Dejo para más adelante:
Tabla usuarios: añadir hora de reseteo y/o zona horaria si lo hago así
crear tabla de logros