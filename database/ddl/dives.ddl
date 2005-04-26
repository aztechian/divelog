CREATE TABLE dives(
   diveid           INT PRIMARY KEY,
   userid           INT FOREIGN KEY(userid) REFERENCES users ON DELETE CASCADE,
   divenum          INT NOT NULL, -- This is the sequence number of the particular divers' dive.
   surface_temp     INT,
   visability       INT,
   weight           INT,
   windspeed        INT,
   waves            INT,
   comments         TEXT,
   description      TEXT,
   location_city    TEXT,
   location_state   TEXT,
   location_country TEXT,
   location_coords  POINT
   time_in          TIMESTAMP,
   time_out         TIMESTAMP,
   press_group_in   CHAR(1),
   press_group_out  CHAR(1),
   depth            INT,
   bottom_time      INTERVAL HOUR to MINUTE,
   safety_stop      INT
) WITHOUT OIDS

CREATE TRIGGER dives_insert_trigger BEFORE INSERT ON dives
  FOR EACH ROW
  EXECUTE PROCEDURE auto_increment_dives_pk();

