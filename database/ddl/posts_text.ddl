CREATE TABLE posts_text(
   post_id       INT REFERENCES posts(post_id) ON DELETE CASCADE,
   post_subject  TEXT DEFAULT NULL,
   post_text     TEXT DEFAULT NULL
) WITHOUT OIDS;

