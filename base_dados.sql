-- Criar Base de Dados
CREATE DATABASE IF NOT EXISTS premier_league;
USE premier_league;

-- =============================
-- TABELA: equipas (20 equipas da PL 2025/26)
-- =============================
CREATE TABLE equipas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    estadio VARCHAR(100),
    treinador VARCHAR(100)
);

-- Inserir equipas da Premier League 2025/26 com treinadores
INSERT INTO equipas (nome, estadio, treinador) VALUES
('Arsenal', 'Emirates Stadium', 'Mikel Arteta'),
('Aston Villa', 'Villa Park', 'Unai Emery'),
('Bournemouth', 'Vitality Stadium', 'Andoni Iraola'),
('Brentford', 'Gtech Community Stadium', 'Keith Andrews'),
('Brighton & Hove Albion', 'Amex Stadium', 'Fabian Hurzeler'),
('Burnley', 'Turf Moor', 'Scott Parker'),
('Chelsea', 'Stamford Bridge', 'Enzo Maresca'),
('Crystal Palace', 'Selhurst Park', 'Oliver Glasner'),
('Everton', 'Goodison Park', 'David Moyes'),
('Fulham', 'Craven Cottage', 'Marco Silva'),
('Leeds United', 'Elland Road', 'Daniel Farke'),
('Liverpool', 'Anfield', 'Arne Slot'),
('Manchester City', 'Etihad Stadium', 'Pep Guardiola'),
('Manchester United', 'Old Trafford', 'Ruben Amorim'),
('Newcastle United', 'St. James''s Park', 'Eddie Howe'),
('Nottingham Forest', 'City Ground', 'Nuno Espírito Santo'),
('Sunderland', '–', 'Regis Le Bris'),
('Tottenham Hotspur', 'Tottenham Hotspur Stadium', 'Thomas Frank'),
('West Ham United', 'London Stadium', 'Graham Potter'),
('Wolverhampton Wanderers', 'Molineux Stadium', 'Vitor Pereira');

-- =============================
-- TABELA: jornadas (38)
-- =============================
CREATE TABLE jornadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    data_jornada DATE
);

INSERT INTO jornadas (numero, data_jornada) VALUES
(1, NULL), (2, NULL), (3, NULL), (4, NULL), (5, NULL),
(6, NULL), (7, NULL), (8, NULL), (9, NULL), (10, NULL),
(11, NULL), (12, NULL), (13, NULL), (14, NULL), (15, NULL),
(16, NULL), (17, NULL), (18, NULL), (19, NULL), (20, NULL),
(21, NULL), (22, NULL), (23, NULL), (24, NULL), (25, NULL),
(26, NULL), (27, NULL), (28, NULL), (29, NULL), (30, NULL),
(31, NULL), (32, NULL), (33, NULL), (34, NULL), (35, NULL),
(36, NULL), (37, NULL), (38, NULL);

-- =============================
-- TABELA: jogos (liga os jogos a jornadas e equipas)
-- =============================
CREATE TABLE jogos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_jornada INT NOT NULL,
    equipa_casa INT NOT NULL,
    equipa_fora INT NOT NULL,
    data_jogo DATETIME,
    FOREIGN KEY (id_jornada) REFERENCES jornadas(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (equipa_casa) REFERENCES equipas(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (equipa_fora) REFERENCES equipas(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- =============================
-- TABELA: resultados (placares dos jogos)
-- =============================
CREATE TABLE resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_jogo INT NOT NULL,
    golos_casa INT DEFAULT 0,
    golos_fora INT DEFAULT 0,
    FOREIGN KEY (id_jogo) REFERENCES jogos(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);
