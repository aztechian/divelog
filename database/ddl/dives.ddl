CREATE TABLE dives(
   diveid           SERIAL PRIMARY KEY,
   userid           INT REFERENCES users(userid) ON DELETE CASCADE,
   divenum          INT NOT NULL,
   surface_temp     SMALLINT,
   visability       SMALLINT,
   weight           SMALLINT,
   windspeed        SMALLINT,
   waves            SMALLINT,
   comments         TEXT,
   description      TEXT,
   location_city    TEXT,
   location_state   TEXT,
   location_country TEXT,
   location_coords  POINT,
   time_in          TIMESTAMP(0) WITH TIME ZONE,
   time_out         TIMESTAMP(0) WITH TIME ZONE,
   press_group_in   CHAR(1),
   press_group_out  CHAR(1),
   depth            SMALLINT,
   bottom_time      INTERVAL HOUR to MINUTE,
   safety_stop      SMALLINT
) WITHOUT OIDS;

COMMENT ON COLUMN dives.divenum IS 'This is the sequence number of the particular divers dive.'
