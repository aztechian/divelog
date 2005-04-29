CREATE TABLE rdp3 (
    pressure_group  CHAR(1),
    depth           SMALLINT,
    residual        INTERVAL hour to minute,
    max_bt          INTERVAL hour to minute
) WITHOUT OIDS;


