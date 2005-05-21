--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: do_clear_sessions
--   Input:    None.
--   Output:   None.
--   Returns:  None. Side effect of clearing out any expired sessions from
--             the sessions table.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION do_clear_sessions() RETURNS void AS'
DECLARE
   curtime timestamp := ''now'';
BEGIN
   DELETE FROM sessions WHERE session_time < curtime;
   RETURN;
END;
' LANGUAGE plpgsql;

