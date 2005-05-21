--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: do_user_login
--   Input:    The username of the user to log in, The IP address of the
--             user logging in, and the session ID for this users' session.
--   Output:   None.
--   Returns:  The session ID given in the 3rd parameter. Raises exception
--             if the given username does not exist. The functions ins_session
--             or upd_session should have been previously called by the
--             application.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION do_user_login(TEXT,TEXT,TEXT) RETURNS TEXT AS'
DECLARE
   uname ALIAS FOR $1;
   ip ALIAS FOR $2;
   sessid ALIAS FOR $3;
   uid int;
   curtime timestamp := ''NOW'';
   exptime timestamp := timestamp ''NOW'' + interval ''3 hours'';
BEGIN
   SELECT INTO uid userid FROM users WHERE username=uname;
   IF NOT FOUND THEN
      RAISE EXCEPTION ''user % not found'', uname;
   END IF;

   UPDATE sessions SET session_user_id=uid, session_time=exptime, session_ip=ip, session_logged_in=true WHERE session_id=sessid;
   RETURN sessid;
END;
' LANGUAGE plpgsql;

