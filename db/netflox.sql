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
    id            BIGSERIAL PRIMARY KEY
  , link          TEXT      UNIQUE
  , gestor_id     BIGINT    NOT NULL
                            REFERENCES gestores_archivos (id)
                            ON DELETE NO ACTION
                            ON UPDATE CASCADE
);

-- Usuarios
DROP TABLE IF EXISTS usuarios CASCADE;
CREATE TABLE usuarios
(
    id         BIGSERIAL    PRIMARY KEY
  , nick       VARCHAR(50)  NOT NULL UNIQUE
                            CONSTRAINT ck_nick_sin_espacios
                            CHECK (nick NOT LIKE '% %')
  , email      VARCHAR(255) NOT NULL UNIQUE
  , biografia  VARCHAR(255)
  , imagen_id  BIGINT       NOT NULL REFERENCES archivos (id)
                            ON DELETE NO ACTION
                            ON UPDATE CASCADE
  , created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
  , banned_at  TIMESTAMP
  , token      VARCHAR(32)
  , password   VARCHAR(60)  NOT NULL
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
DROP TABLE IF EXISTS duraciones CASCADE;
CREATE TABLE duraciones
(
    id      BIGSERIAL    PRIMARY KEY
  , tipo    VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS tipos CASCADE;
CREATE TABLE tipos
(
    id          BIGSERIAL    PRIMARY KEY
  , tipo        VARCHAR(255) NOT NULL UNIQUE
  , duracion_id BIGINT       NOT NULL REFERENCES duraciones (id)
                             ON DELETE NO ACTION
                             ON UPDATE CASCADE
  , padre_id  BIGINT
);
ALTER TABLE tipos 
ADD CONSTRAINT fk1_relacion_involutiva_tipos
FOREIGN KEY (padre_id) REFERENCES tipos (id) ON DELETE NO ACTION ON UPDATE CASCADE;

DROP TABLE IF EXISTS shows CASCADE;
CREATE TABLE shows
(
    id               BIGSERIAL    PRIMARY KEY
  , titulo           VARCHAR(255) NOT NULL
  , sinopsis         TEXT
  , lanzamiento      DATE         NOT NULL
  , duracion         SMALLINT
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

DROP TABLE IF EXISTS generos CASCADE;
CREATE TABLE generos
(
    id     BIGSERIAL    PRIMARY KEY
  , genero VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS shows_generos CASCADE;
CREATE TABLE shows_generos
(
    id        BIGSERIAL PRIMARY KEY
  , show_id   BIGINT    NOT NULL 
                        REFERENCES shows (id)
                        ON DELETE NO ACTION
                        ON UPDATE CASCADE
  , genero_id BIGINT    NOT NULL
                        REFERENCES generos (id)
                        ON DELETE NO ACTION
                        ON UPDATE CASCADE
  , UNIQUE (show_id, genero_id)
);

DROP TABLE IF EXISTS shows_descargas CASCADE;
CREATE TABLE shows_descargas
(
    id            BIGSERIAL  PRIMARY KEY
  , num_descargas BIGINT     DEFAULT 0
  , archivo_id    BIGINT     NOT NULL 
                             REFERENCES archivos (id)
                             ON DELETE NO ACTION
                             ON UPDATE CASCADE
  , show_id       BIGINT     NOT NULL 
                             REFERENCES shows (id)
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
    id              BIGSERIAL PRIMARY KEY
  , cuerpo          TEXT
  , valoracion      INT       -- Nota del show valorado.
  , created_at      TIMESTAMP NOT NULL
                              DEFAULT CURRENT_TIMESTAMP
  , padre_id        BIGINT
  , show_id         BIGINT    NOT NULL
                              REFERENCES shows (id)
                              ON DELETE NO ACTION
                              ON UPDATE CASCADE
  , usuario_id      BIGINT    NOT NULL
                              REFERENCES usuarios (id)
                              ON DELETE NO ACTION
                              ON UPDATE CASCADE
  -- TODO: En caso de ser una valoración, el usuario_id y el show_id en conjunto son uniques y la valoracion debe ser null
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
     , ('EMBED')
     , ('uTorrent');

INSERT INTO archivos (link, gestor_id)
VALUES ('user.jpeg', 2)
     , ('jedi.jpg', 2)
     , ('jedi.mp4', 2)
     , ('interestelar.jpg', 2)
     , ('interestelar.mp4', 2)
     , ('endgame.jpg', 2)
     , ('endgame.mp4', 2)
     , ('ahs.jpg', 2)
     , ('ahs.mp4', 2)
     , ('marvel.jpg', 2);

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

INSERT INTO duraciones (tipo)
VALUES ('peliculas')
     , ('temporadas')
     , ('episodios')
     , ('horas')
     , ('minutos')
     , ('paginas');

INSERT INTO tipos (tipo, duracion_id, padre_id)
VALUES ('Pelicula', 5, 5)
     , ('Serie', 2, NULL)
     , ('Temporada', 3, 2)
     , ('Episodio', 5, 3)
     , ('Saga de peliculas', 1, NULL);

INSERT INTO shows (titulo, imagen_id, trailer_id, lanzamiento, duracion, sinopsis, tipo_id, show_id)
VALUES ('Los últimos Jedi', 2, 3, '2016-06-23', 204, 'La Primera Orden ha acorralado a los últimos miembros de la resistencia. Su última esperanza es que Finn se introduzca en la nave de Snoke y desactive el radar que les permite localizarlos. Mientras él trata, en compañía de una soldado de la Resistencia, de cumplir esta misión imposible, Rey se encuentra lejos, intentando convencer a Luke Skywalker de que la entrene y la convierta en la última jedi.', 1, NULL) -- Pelicula: id=1
     , ('Interestelar', 4, 5, '2016-06-23', 204, 'Gracias a un descubrimiento, un grupo de científicos y exploradores, encabezados por Cooper, se embarcan en un viaje espacial para encontrar un lugar con las condiciones necesarias para reemplazar a la Tierra y comenzar una nueva vida allí.', 1, NULL) -- Pelicula: id=2
     , ('Marvel', 10, NULL, '2016-06-23', 23, '', 5, NULL) -- Saga: id=3
     , ('Avengers: ENDGAME', 6, 7, '2016-06-23', NULL, 'El grave curso de los acontecimientos puestos en marcha por Thanos, que destruyó a la mitad del universo y fracturó las filas de los Vengadores, obliga a los Vengadores restantes a prepararse para una última batalla en la gran conclusión de las 22 películas de Marvel Studios, Avengers: Endgame.', 1, 3) -- Pelicula: id=4
     , ('American Horror Story', 8, 9, '2011-06-23', 8, 'American Horror Story esta es una serie de televisión de drama y horror creada y producida por Ryan Murphy y Brad Falchuk. Es una serie de antología, ya que cada temporada se realiza como una miniserie independiente, con un grupo de personajes diferentes, escenarios distintos y una trama que tiene su propio comienzo, desarrollo y final. Aun así, las temporadas están conectadas entre sí.', 2, NULL) -- Serie: id=5
     , ('Murder House', 8, 9, '2011-06-23', 12, 'La primera temporada, retitulada American Horror Story: Murder House tiene lugar en el 2011 y sigue a la familia Harmon: Ben de profesión psiquiatra, su esposa Vivien, y su hija adolescente Violet, quienes se mudan de Boston a Los Ángeles después de que Vivien tenga un aborto involuntario y Ben una aventura con una de sus alumnas. La familia se muda a una casa restaurada, y pronto se encontrarán con los antiguos residentes de la casa, los Langdon: Constance, su hija Addie y el desfigurado Larry Harvey. Ben y Vivien intentan reavivar su relación, mientras Violet está sufriendo de depresión, encuentra consuelo con Tate, un paciente de su padre. Los Langdon y Larry influyen con frecuencia en la vida de los Harmon, ya que la familia descubre que la casa está embrujada por todos los que murieron en ella. Entre los cuales están las fantasmas Moira, el ama de llaves y la primera dueña, Nora Montgomery.', 3, 5) -- Temporada: id=6
     , ('Pilot', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=7
     , ('Home Invasion', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=8
     , ('Murder House', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=9
     , ('Halloween (Part 1)', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=10
     , ('Halloween (Part 2)', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=11
     , ('Piggy Piggy', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=12
     , ('Open House', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=13
     , ('Rubber Man', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=14
     , ('Spooky Little Girl', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=15
     , ('Smoldering Children', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=16
     , ('Birth', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=17
     , ('Afterbirth', NULL, NULL, '2016-06-23', 42, '', 4, 6) -- Capitulo: id=18
     , ('Asylum', 8, 9, '2016-06-23', 13, 'La historia tiene lugar en 1964, en el Manicomio Católico Briarcliff, construido en 1908, que funcionó como el centro de tuberculosis más grande del país, donde murieron 46.000 personas. En 1962 el lugar es comprado por la Iglesia Católica y convertido en un manicomio para criminales dementes dirigido por el Monseñor Timothy Howard. Allí es enviado Kit Walker, un joven que trabajaba en una gasolinera, acusado de haber matado a su esposa Alma Walker. Kit es llevado al manicomio siendo confundido con el asesino en serie Bloody Face, quien despellejaba a sus víctimas (siempre mujeres) y les cortaba la cabeza. Pero en realidad, Alma fue secuestrada por extraterrestres junto con su esposo, regresando solo este último a la Tierra. Nadie cree la historia de Kit, excepto Grace, una interna. Dentro del manicomio, se encontrará con terroríficos personajes como el doctor Arthur Arden y la hermana Jude, quien encierra a Lana Winters, una periodista lesbiana curiosa acerca de Bloody Face. Las cosas se complican dentro del asilo cuando la hermana Mary Eunice, monja protegida de la hermana Jude, sufre una posesión satánica. Extraños sucesos tienen lugar en el manicomio, en donde reinan el horror, la injusticia, la demencia, el trato inhumano y el dolor.', 3, 5)  -- Temporada: id=19
     , ('Welcome to Briarcliff', NULL, NULL, '2016-06-23', 42, '', 4, 19) -- Capitulo: id=20
     , ('Tricks and Treats', NULL, NULL, '2016-06-23', 42, '', 4, 19) -- Capitulo: id=21
     , ('The Origins of Monstrosity', NULL, NULL, '2016-06-23', 42, '', 4, 19) -- Capitulo: id=22
     , ('Dark Coussins', NULL, NULL, '2016-06-23', 42, '', 4, 19) -- Capitulo: id=23
     , ('Covem', 8, 9, '2013-06-23', 13, 'La temática de la historia es acerca de la brujería. Se sitúa en 2013, con flashbacks del siglo XIX. Cuando la familia de Zoe Benson descubre que tiene habilidades diferentes es enviada a Miss Robicheaux Academy, instituto que presenta una crisis debido a la posible extinción de las descendientes de Salem, donde encuentra a tres jóvenes brujas más, la caprichosa y vanidosa Madison Montgomery, Queenie, una muñeca vudú humana, y Nan, quien posee clarividencia. Cordelia Foxx, directora del instituto y su madre Fiona Goode, bruja Suprema del aquelarre (la más poderosa). Hacen lo posible por mantener su linaje en pie, luchando contra sus enemigos, los cazadores de brujas y la reina Vudú Marie Laveau. Mientras que Fiona, en la búsqueda de sus intereses personales se encuentra con la sádica racista Delphine LaLaurie, inmortal debido a un hechizo Vudú desde el siglo XIX por culpa de Laveau. La historia se complica con los intentos de la bruja fanática de la moda y líder del Consejo de brujas Myrtle Snow de sacar a flote las perversas intenciones de Fiona, así como también la llegada de la resucitada bruja del pantano Misty Day. Sus temas principales son la opresión y sobre usar todo el potencial que tenemos, así como también la necesidad de reconocer y pertenecer a una "tribu".', 3, 5); -- Temporada: id=24

INSERT INTO generos (genero)
VALUES ('Comedia')
     , ('Terror')
     , ('Ciencia-Ficción')
     , ('Drama')
     , ('Aventuras');

INSERT INTO shows_generos (show_id, genero_id)
VALUES (1, 3), (2, 3), (2, 4)
     , (3, 1), (3, 3), (3, 4)
     , (4, 4), (4, 1), (4, 3)
     , (5, 2), (6, 2), (19, 2)
     , (24, 2);

INSERT INTO shows_descargas (show_id, archivo_id)
VALUES (1, 3), (2, 5), (4, 7)
     , (7, 9), (8, 9), (9, 9)
     , (10, 9), (11, 9), (12, 9)
     , (13, 9), (14, 9), (15, 9)
     , (16, 9), (17, 9), (18, 9)
     , (20, 9), (21, 9), (22, 9)
     , (23, 9);

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

INSERT INTO comentarios (cuerpo, valoracion, show_id, padre_id, usuario_id)
VALUES ('Pelicula muy buena!', 5, 2, NULL, 3)
     , ('Una de las mejores sagas bajo mi criterio', 4, 3, NULL, 4)
     , ('Saga poco cientifica, dejando eso de lado, bien', 3, 3, NULL, 1)
     , ('No me gusta el terror', 1, 1, NULL, 2)
     , ('Eso lo diras tu, caranchoa', NULL, 1, 3, 3);

INSERT INTO votos (usuario_id, comentario_id, votacion)
VALUES (1, 2, 1)
     , (2, 1, 1)
     , (2, 2, 1)
     , (2, 3, 1)
     , (2, 4, 1)
     , (3, 2, -1)
     , (3, 1, -1);