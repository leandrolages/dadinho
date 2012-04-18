CREATE
    TABLE jogador
    (
        id_jogador bigint unsigned NOT NULL AUTO_INCREMENT,
        id_sala bigint unsigned,
        nome VARCHAR(25) NOT NULL,
        ultima_acao bigint unsigned,
        PRIMARY KEY (id_jogador),
        CONSTRAINT fk1 FOREIGN KEY (id_sala) REFERENCES sala (id_sala) ON
    DELETE
        SET NULL ON
    UPDATE
        CASCADE,
        INDEX fk1 (id_sala)
    )
    ENGINE=InnoDB DEFAULT CHARSET=utf8