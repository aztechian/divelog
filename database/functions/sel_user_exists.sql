--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_user_exists
--   Input:    The username of the user to be determined it is valid
--   Output:   None.
--   Returns:  A boolean value indicating that the given name exists in the
--             system.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_user_exists(TEXT) RETURNS boolean AS '
DECLARE
   name record;
BEGIN
   SELECT INTO name userid FROM users WHERE username=$1;
   IF NOT FOUND THEN
      RETURN false;
   END IF;
   RETURN true;
END;
' LANGUAGE plpgsql;

