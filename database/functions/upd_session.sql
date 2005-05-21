--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: upd_session
--   Input:    The session ID of the session that should be refreshed.
--   Output:   None.
--   Returns:  A boolean value indicating that the session ID given was
--             successfully updated so that it expires three hours in the
--             future.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION upd_session(TEXT) RETURNS boolean AS'
DECLARE
   sid ALIAS FOR $1;
   curtime timestamp := ''now'';
   tempsid text;
BEGIN
   SELECT INTO tempsid session_id FROM sessions WHERE session_id=sid;
   IF NOT FOUND THEN
      RETURN false;
   END IF;
   UPDATE sessions SET session_time=(curtime + interval ''3 hours'') WHERE session_id=sid;
   RETURN true;
END;
' LANGUAGE plpgsql;

