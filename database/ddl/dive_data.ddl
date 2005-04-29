CREATE TABLE dive_data(
   time           TIMESTAMP(0) PRIMARY KEY,
   diveid         INT REFERENCES dives(diveid) ON DELETE CASCADE,
   depth          SMALLINT,
   temp           DEC(3,2),
   bottom_time    INTERVAL HOUR to SECOND,
   bt_remaining   INTERVAL HOUR to SECOND,
   tank_press     SMALLINT,
   nitrogen_load  TEXT
) WITHOUT OIDS;
