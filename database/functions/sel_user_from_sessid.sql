--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_user_from_sessid
--   Input:    The string representing the session id to use.
--   Output:   None.
--   Returns:  The user ID and name matching the session.
--
--/////////////////////////////////////////////////////////////////////////

CREATE OR REPLACE FUNCTION sel_user_from_sessid(TEXT) RETURNS record AS '
DECLARE
	sid ALIAS FOR $1;

	date timestamp := ''now'';
	uid record;
BEGIN
	SELECT INTO uid u.userid,u.username FROM sessions s, users u 
	   WHERE s.session_id = sid 
	   AND s.session_time > date AND s.session_user_id = u.userid;
	IF NOT FOUND THEN
		RETURN uid;
	END IF;
	RETURN uid;
END;
' LANGUAGE plpgsql;

