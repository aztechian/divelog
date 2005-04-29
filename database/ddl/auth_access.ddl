CREATE TABLE auth_access(
   userid        INT PRIMARY KEY REFERENCES users(userid),
   auth_view     BOOLEAN NOT NULL DEFAULT 'false',
   auth_read     BOOLEAN NOT NULL DEFAULT 'false',
   auth_post     BOOLEAN NOT NULL DEFAULT 'false',
   auth_reply    BOOLEAN NOT NULL DEFAULT 'false',
   auth_edit     BOOLEAN NOT NULL DEFAULT 'false',
   auth_delete   BOOLEAN NOT NULL DEFAULT 'false',
   auth_mod      BOOLEAN NOT NULL DEFAULT 'false'
) WITHOUT OIDS;

