--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: do_user_logout
--   Input:    The username of the user to be logged out.
--   Output:   None.
--   Returns:  None. Side effect of clearing out the users' session in the
--             sessions table and updating the users table to set the users'
--             status to indicate they are logged out.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION do_user_logout(TEXT) RETURNS void AS'
DECLARE
   sid ALIAS FOR $1;
   uid int;
   curtime timestamp := ''now'';
BEGIN
   SELECT INTO uid session_user_id FROM sessions WHERE session_id=sid;
   DELETE FROM sessions WHERE session_id=sid;
   UPDATE users SET lastvisit=curtime WHERE userid=uid;
   RETURN;
END;
' LANGUAGE plpgsql;

