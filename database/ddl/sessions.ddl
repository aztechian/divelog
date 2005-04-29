CREATE TABLE sessions(
   session_id         VARCHAR(32) PRIMARY KEY,
   session_user_id    INT REFERENCES users(userid) ON DELETE CASCADE,
   session_start      TIMESTAMP(0) WITH TIME ZONE,
   session_time       TIMESTAMP(0) WITH TIME ZONE,
   session_ip         VARCHAR(15),
   session_logged_in  BOOLEAN NOT NULL DEFAULT 'false'
) WITHOUT OIDS;

