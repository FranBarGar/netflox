-- Persistencia de datos:
-- En caso de borrar un usuario, se eliminaria toda su informacion incluyendo 
-- sus comentarios, manteniendo los comentarios que dependen de el gracias a 
-- un usuario fantasma como por ejemplo: "Anonymous".

--Almacenamiento
DROP TABLE IF EXISTS gestores_archivos CASCADE;
CREATE TABLE gestores_archivos
(
    id     BIGSERIAL    PRIMARY KEY
  , nombre VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS archivos CASCADE;
CREATE TABLE archivos
(
    id            BIGSERIAL  PRIMARY KEY
  , link          TEXT       UNIQUE
  , gestor_id     BIGINT     NOT NULL
                             REFERENCES gestores_archivos (id)
                             ON DELETE NO ACTION
                             ON UPDATE CASCADE
);

-- Usuarios
DROP TABLE IF EXISTS usuarios CASCADE;
CREATE TABLE usuarios
(
    id         BIGSERIAL      PRIMARY KEY
  , nick       VARCHAR(50)    NOT NULL UNIQUE
                              CONSTRAINT ck_nick_sin_espacios
                              CHECK (nick NOT LIKE '% %')
  , email      VARCHAR(255)   NOT NULL UNIQUE
  , imagen_id  BIGINT         NOT NULL REFERENCES archivos (id)
                              ON DELETE NO ACTION
                              ON UPDATE CASCADE
  , created_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
  , banned_at  TIMESTAMP
  , token      VARCHAR(32)
  , password   VARCHAR(60)    NOT NULL
);

DROP TABLE IF EXISTS seguidores CASCADE;
CREATE TABLE seguidores
(
    id          BIGSERIAL PRIMARY KEY
  , created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  , seguidor_id BIGINT    NOT NULL REFERENCES usuarios (id)
                          ON DELETE CASCADE
                          ON UPDATE CASCADE
  , seguido_id  BIGINT    NOT NULL REFERENCES usuarios (id)
                          ON DELETE CASCADE
                          ON UPDATE CASCADE
  , UNIQUE (seguidor_id, seguido_id)
);

-- Peliculas, series, temporadas y capitulos
DROP TABLE IF EXISTS tipos CASCADE;
CREATE TABLE tipos
(
    id     BIGSERIAL    PRIMARY KEY
  , tipo   VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS duraciones CASCADE;
CREATE TABLE duraciones
(
    id      BIGSERIAL    PRIMARY KEY
  , tipo    VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS generos CASCADE;
CREATE TABLE generos
(
    id     BIGSERIAL    PRIMARY KEY
  , genero VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS shows CASCADE;
CREATE TABLE shows
(
    id               BIGSERIAL    PRIMARY KEY
  , titulo           VARCHAR(255) NOT NULL
  , sinopsis         TEXT
  , lanzamiento      DATE         NOT NULL
  , duracion         SMALLINT
  , duracion_id      BIGINT       REFERENCES duraciones (id)
                                  ON DELETE NO ACTION
                                  ON UPDATE CASCADE
  , imagen_id        BIGINT       REFERENCES archivos (id)
                                  ON DELETE NO ACTION
                                  ON UPDATE CASCADE
  , trailer_id       BIGINT       REFERENCES archivos (id)
                                  ON DELETE NO ACTION
                                  ON UPDATE CASCADE
  , tipo_id          BIGINT       NOT NULL
                                  REFERENCES tipos (id)
                                  ON DELETE NO ACTION
                                  ON UPDATE CASCADE
  , show_id          BIGINT
);
ALTER TABLE shows 
ADD CONSTRAINT fk1_relacion_involutiva_shows
FOREIGN KEY (show_id) REFERENCES shows (id) ON DELETE NO ACTION ON UPDATE CASCADE;

CREATE INDEX idx_shows_lanzamiento ON shows (lanzamiento);

DROP TABLE IF EXISTS shows_generos CASCADE;
CREATE TABLE shows_generos
(
    id           BIGSERIAL  PRIMARY KEY
  , show_id      BIGINT     NOT NULL 
                            REFERENCES shows (id)
                            ON DELETE NO ACTION
                            ON UPDATE CASCADE
  , genero_id    BIGINT     NOT NULL
                            REFERENCES generos (id)
                            ON DELETE NO ACTION
                            ON UPDATE CASCADE
  , UNIQUE (show_id, genero_id)
);

DROP TABLE IF EXISTS shows_descargas CASCADE;
CREATE TABLE shows_descargas
(
    id                              BIGSERIAL  PRIMARY KEY
  , num_descargas                   BIGINT     DEFAULT 0
  , archivo_id           BIGINT     NOT NULL 
                                    REFERENCES archivos (id)
                                    ON DELETE NO ACTION
                                    ON UPDATE CASCADE
  , show_id              BIGINT     NOT NULL REFERENCES shows (id)
                                    ON DELETE NO ACTION
                                    ON UPDATE CASCADE
  , UNIQUE (show_id, archivo_id)
);

-- Participantes
DROP TABLE IF EXISTS personas CASCADE;
CREATE TABLE personas
(
    id     BIGSERIAL    PRIMARY KEY
  , nombre VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS roles CASCADE;
CREATE TABLE roles
(
    id  BIGSERIAL    PRIMARY KEY
  , rol VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS participantes CASCADE;
CREATE TABLE participantes
(
    id          BIGSERIAL PRIMARY KEY
  , show_id     BIGINT    NOT NULL 
                          REFERENCES shows (id)
                          ON DELETE NO ACTION
                          ON UPDATE CASCADE
  , persona_id  BIGINT    NOT NULL 
                          REFERENCES personas (id)
                          ON DELETE NO ACTION
                          ON UPDATE CASCADE
  , rol_id      BIGINT    NOT NULL 
                          REFERENCES roles (id)
                          ON DELETE NO ACTION
                          ON UPDATE CASCADE
  , UNIQUE (show_id, persona_id, rol_id)
);

-- Comentarios/valoraciones y votos
DROP TABLE IF EXISTS comentarios CASCADE;
CREATE TABLE comentarios
(
    id              BIGSERIAL   PRIMARY KEY
  , cuerpo          TEXT
  , votacion        INT         -- Nota del show valorado.
  , created_at      TIMESTAMP   NOT NULL
                                DEFAULT CURRENT_TIMESTAMP
  , show_id         BIGINT      NOT NULL
                                REFERENCES shows (id)
                                ON DELETE NO ACTION
                                ON UPDATE CASCADE
  , padre_id        BIGINT
  , usuario_id      BIGINT      NOT NULL
                                REFERENCES usuarios (id)
                                ON DELETE NO ACTION
                                ON UPDATE CASCADE
  -- TODO: En caso de ser una valoración, el usuario_id y el show_id en conjunto son uniques
);
ALTER TABLE comentarios 
ADD CONSTRAINT fk1_relacion_involutiva_comentarios 
FOREIGN KEY (padre_id) REFERENCES comentarios (id) ON DELETE NO ACTION ON UPDATE CASCADE;

DROP TABLE IF EXISTS votos CASCADE;
CREATE TABLE votos
(
      id            BIGSERIAL PRIMARY KEY
    , usuario_id    BIGINT    NOT NULL
                              REFERENCES usuarios(id)
                              ON DELETE CASCADE
                              ON UPDATE CASCADE
    , comentario_id BIGINT    NOT NULL
                              REFERENCES comentarios(id)
                              ON DELETE CASCADE
                              ON UPDATE CASCADE
    , votacion      INT
    , UNIQUE(usuario_id, comentario_id)
);

-- TODO: Plan to watch

-- TODO: Dropped

-- TODO: Shows vistos

-- INSERT
INSERT INTO gestores_archivos (nombre)
VALUES ('AWS')
     , ('Local')
     , ('MEGA')
     , ('uTorrent');

-- INSERT archivos TERMINAR
INSERT INTO archivos (link, gestor_id)
VALUES ('user.jpeg', 2)
     , ('jedi.jpg', 2)
     , ('jedi.mp4', 2)
     , ('jed.jpg', 2)
     , ('jed.mp4', 2)
     , ('je.jpg', 2)
     , ('je.mp4', 2)
     , ('j.jpg', 2)
     , ('j.mp4', 2);

INSERT INTO usuarios (nick, email, imagen_id, password)
VALUES ('pepe', 'pepe@pepe.com', 1, crypt('pepe', gen_salt('bf', 10)))
     , ('admin', 'admin@admin.com', 1, crypt('admin', gen_salt('bf', 10)))
     , ('xhama', 'xhama@xhama.com', 1, crypt('xhama', gen_salt('bf', 10)))
     , ('hypra', 'hypra@hypra.com', 1, crypt('hypra', gen_salt('bf', 10)))
     , ('federico', 'federico@federico.com', 1, crypt('federico', gen_salt('bf', 10)));

INSERT INTO seguidores (created_at, seguidor_id, seguido_id)
VALUES (CURRENT_TIMESTAMP, 1, 2)
     , (CURRENT_TIMESTAMP, 2, 1)
     , (CURRENT_TIMESTAMP, 3, 2)
     , (CURRENT_TIMESTAMP, 3, 1)
     , (CURRENT_TIMESTAMP, 3, 4)
     , (CURRENT_TIMESTAMP, 4, 2);

INSERT INTO tipos (tipo)
VALUES ('Pelicula')
     , ('Serie')
     , ('Temporada')
     , ('Capitulo')
     , ('Saga');

INSERT INTO duraciones (tipo)
VALUES ('peliculas')
     , ('temporadas')
     , ('capitulos')
     , ('horas')
     , ('minutos')
     , ('paginas');

INSERT INTO generos (genero)
VALUES ('Comedia')
     , ('Terror')
     , ('Ciencia-Ficción')
     , ('Drama')
     , ('Aventuras');

INSERT INTO shows (titulo, imagen_id, trailer_id, lanzamiento, duracion, duracion_id, sinopsis, tipo_id, show_id)
VALUES ('Los últimos Jedi', 2, 3, '2016-06-23', 204, 5, 'La Primera Orden ha acorralado a los últimos miembros de la resistencia. Su última esperanza es que Finn se introduzca en la nave de Snoke y desactive el radar que les permite localizarlos. Mientras él trata, en compañía de una soldado de la Resistencia, de cumplir esta misión imposible, Rey se encuentra lejos, intentando convencer a Luke Skywalker de que la entrene y la convierta en la última jedi.', 1, NULL) -- Pelicula: id=1
     , ('Interestelar', 4, 5, '2016-06-23', 204, 5, 'Gracias a un descubrimiento, un grupo de científicos y exploradores, encabezados por Cooper, se embarcan en un viaje espacial para encontrar un lugar con las condiciones necesarias para reemplazar a la Tierra y comenzar una nueva vida allí.', 1, NULL) -- Pelicula: id=2
     , ('Avengers: ENDGAME', 6, 7, '2016-06-23', NULL, NULL, 'El grave curso de los acontecimientos puestos en marcha por Thanos, que destruyó a la mitad del universo y fracturó las filas de los Vengadores, obliga a los Vengadores restantes a prepararse para una última batalla en la gran conclusión de las 22 películas de Marvel Studios, Avengers: Endgame.', 1, NULL) -- Pelicula: id=3
     , ('American Horror Story', 8, 9, '2011-06-23', 8, 2, '', 2, NULL) -- Serie: id=4
     , ('Murder House', 8, 9, '2011-06-23', 12, 3, '', 3, 4) -- Temporada: id=5
     , ('Pilot', NULL, NULL, '2016-06-23', 42, 5, '', 4, 15) -- Capitulo: id=6
     , ('Home Invasion', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=7
     , ('Murder House', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=NULL
     , ('Halloween (Part 1)', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=9
     , ('Halloween (Part 2)', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=10
     , ('Piggy Piggy', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=11
     , ('Open House', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=12
     , ('Rubber Man', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=13
     , ('Spooky Little Girl', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=14
     , ('Smoldering Children', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=15
     , ('Birth', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=16
     , ('Afterbirth', NULL, NULL, '2016-06-23', 42, 5, '', 4, 5) -- Capitulo: id=17
     , ('Asylum', 8, 9, '2016-06-23', 13, 3, '', 3, 4)  -- Temporada: id=18
     , ('Welcome to Briarcliff', NULL, NULL, '2016-06-23', 42, 5, '', 4, 18) -- Capitulo: id=19
     , ('Tricks and Treats', NULL, NULL, '2016-06-23', 42, 5, '', 4, 18) -- Capitulo: id=20
     , ('The Origins of Monstrosity', NULL, NULL, '2016-06-23', 42, 5, '', 4, 18) -- Capitulo: id=21
     , ('Dark Coussins', NULL, NULL, '2016-06-23', 42, 5, '', 4, 18) -- Capitulo: id=22
     , ('Covem', 8, 9, '2013-06-23', 13, 3, '', 3, 4); -- Temporada: id=23

-- INSERT shows_generos TERMINAR
INSERT INTO shows_generos (show_id, genero_id)
VALUES (1, 2)
     , (2, 1)
     , (3, 2)
     , (3, 1)
     , (3, 4);

-- INSERT shows_descargas TERMINAR
INSERT INTO shows_descargas (archivo_id, show_id)
VALUES (1, 3)
     , (2, 5)
     , (3, 7)
     , (4, 9)
     , (5, 9);

-- INSERT personas RELLENAR
INSERT INTO personas (nombre)
VALUES ('Silverster Stallone')
     , ('Chiquito de la Calzada');

INSERT INTO roles (rol)
VALUES ('Director')
     , ('Productor')
     , ('Intérprete');

-- INSERT participantes TERMINAR
INSERT INTO participantes (show_id, persona_id, rol_id)
VALUES (1, 2, 1)
     , (2, 1, 2)
     , (3, 2, 3)
     , (3, 1, 3);

-- INSERT comentarios TERMINAR
INSERT INTO comentarios (cuerpo, votacion, show_id, padre_id, usuario_id)
VALUES ('Pelicula muy buena!', 4, 1, NULL, 3)
     , ('Pelicula muy buena!', 4, 1, NULL, 3)
     , ('Pelicula muy buena!', 4, 1, NULL, 3)
     , ('Pelicula muy buena!', 4, 1, NULL, 3);

-- INSERT votos TERMINAR
INSERT INTO votos (usuario_id, comentario_id, votacion)
VALUES (1, 2, 1)
     , (2, 1, 1)
     , (3, 2, -1)
     , (3, 1, -1);