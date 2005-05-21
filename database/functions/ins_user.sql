--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: ins_user
--   Input:    The username to be created, the clear-text password to be
--             associated with the user, the users first name, the users
--             last name, the users email address, the users location, the
--             users timezone.
--   Output:   None.
--   Returns:  An integer representing the user ID of the newly created user.
--             Returns '0' (zero) if there was an error during row creation.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION ins_user(TEXT,TEXT,TEXT,TEXT,TEXT,TEXT,INT) RETURNS int AS '
DECLARE
   name ALIAS FOR $1;
   pass ALIAS FOR $2;
   fname ALIAS FOR $3;
   lname ALIAS FOR $4;
   email ALIAS FOR $5;
   loc ALIAS FOR $6;
   tz ALIAS FOR $7;

   uid int;
   curdate timestamp := ''now'';
BEGIN
   INSERT INTO users(username,password,user_fname,user_lname,user_email,location,timezone,regdate,postcount)
     VALUES(name,md5(pass),fname,lname,email,loc,tz,curdate,0);
   IF NOT FOUND THEN
      RETURN 0;
   END IF;
   SELECT INTO uid userid FROM users WHERE username=name;
   IF NOT FOUND THEN
      RETURN 0;
   END IF;
   RETURN uid;
END;
' LANGUAGE plpgsql;

