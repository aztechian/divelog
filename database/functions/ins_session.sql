--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: ins_session
--   Input:    The session ID that should be created in the sessions table.
--   Output:   None.
--   Returns:  None. Has a side effect of inserting a new row into the sessions
--             table. The row does not hold the user ID associated with the
--             session because the session may not have been created by a
--             log-in sequence in the application.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION ins_session(TEXT) RETURNS void AS'
DECLARE
   curtime timestamp := ''now'';
   exptime timestamp := timestamp ''now'' + interval ''3 hours'';
BEGIN
   INSERT INTO sessions VALUES($1,null,curtime,exptime,null,false);
   RETURN;
END;
' LANGUAGE plpgsql;

