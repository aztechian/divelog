--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_user_creds
--   Input:    The username of the user to return a password for
--   Output:   None.
--   Returns:  The md5 encrypted password of the user specified. This should
--             be compared to the md5 hash of the supplied password to check
--             for a match. In no cases will the function return a decrypted
--             password.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_user_creds(TEXT) RETURNS TEXT AS'
DECLARE
   usern ALIAS FOR $1;
   data text;
BEGIN
   SELECT INTO data password FROM users WHERE username=usern;
   IF NOT FOUND THEN
      RAISE EXCEPTION ''user % not found'', usern;
   END IF;
   RETURN data;
END;
' LANGUAGE plpgsql;

