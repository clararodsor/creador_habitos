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
    esHabito BOOLEAN DEFAULT 0,
    racha INT DEFAULT 0,
    puntosAcumulados INT DEFAULT 0,
    mon BOOLEAN,
    tue BOOLEAN,
    wed BOOLEAN,
    thu BOOLEAN,
    fri BOOLEAN,
    sat BOOLEAN,
    sun BOOLEAN,
    CONSTRAINT fk_idUsuario FOREIGN KEY (idUsuario) REFERENCES usuarios(idUsuario)
);

CREATE TABLE registros (
	idTarea INT,
    fecha DATE DEFAULT (CURRENT_DATE),
    cumplido BOOLEAN,
    pospuesto BOOLEAN,
    puntos INT,
    PRIMARY KEY (idTarea, fecha),
    CONSTRAINT fk_idTarea FOREIGN KEY (idTarea) REFERENCES tareas(idTarea)
);