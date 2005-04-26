CREATE TABLE dives(
   diveid INT PRIMARY KEY,
   userid INT FOREIGN KEY(userid) REFERENCES users ON DELETE CASCADE,
   divenum INT NOT NULL, -- This is the sequence number of the particular divers' dive.
   surface_temp INT,
   visability INT,
   weight INT,
   windspeed INT,
   waves INT,
   comments TEXT,
   description TEXT,
   location_city TEXT,
   location_state TEXT,
   location_country TEXT,
   location_coords POINT
) WITHOUT OIDS
