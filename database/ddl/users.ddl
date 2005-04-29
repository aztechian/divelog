CREATE TABLE users(
   userid       SERIAL PRIMARY KEY,
   username     TEXT NOT NULL UNIQUE,
   password     TEXT NOT NULL,
   user_fname   TEXT,
   user_lname   TEXT,
   user_email   TEXT,
   lastvisit    TIMESTAMP(0),
   location     TEXT,
   timezone     DECIMAL(5,2),
   regdate      TIMESTAMP(0) WITH TIME ZONE NOT NULL DEFAULT current_timestamp,
   postcount    SMALLINT NOT NULL,
   loggedin     BOOLEAN DEFAULT 'false'
) WITHOUT OIDS;

