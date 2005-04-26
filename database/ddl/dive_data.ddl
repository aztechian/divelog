CREATE TABLE dive_data(
   time TIMESTAMP PRIMARY KEY,
   diveid INT FOREIGN KEY(diveid) REFERENCES dives ON DELETE CASCADE,
   depth INT,
   temp DEC(3,2),
   bottom_time INTERVAL HOUR to SECOND,
   bt_remaining INTERVAL HOUR to SECOND,
   tank_press INT,
   nitrogen_load TEXT,
) WITHOUT OIDS
