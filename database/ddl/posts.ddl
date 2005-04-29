CREATE TABLE posts(
   post_id          SERIAL PRIMARY KEY,
   poster_id        INT REFERENCES users(userid) ON DELETE CASCADE,
   post_time        TIMESTAMP(0) WITH TIME ZONE,
   poster_ip        VARCHAR(15),
   post_username    TEXT,
   post_edit_time   TIMESTAMP(0),
   post_edit_count  SERIAL
) WITHOUT OIDS;

