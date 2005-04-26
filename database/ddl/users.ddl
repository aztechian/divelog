CREATE TABLE users(
   userid INT PRIMARY KEY,
   username TEXT NOT NULL UNIQUE,
   password TEXT NOT NULL,
   user_fname TEXT,
   user_lname TEXT,
   user_email TEXT,
   lastvisit TIMESTAMP,
   location TEXT,
   timezone DECIMAL(5,2),
   regdate TIMESTAMP NOT NULL DEFAULT current_timestamp,
   postcount INT NOT NULL,
   loggedin BOOLEAN DEFAULT 'false'
)
WITHOUT OIDS

CREATE TRIGGER users_insert_trigger BEFORE INSERT ON users
  FOR EACH ROW
  EXECUTE PROCEDURE auto_increment_users_pk();

