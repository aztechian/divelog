--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_session_expired
--   Input:    The integer representing a users' ID.
--   Output:   None.
--   Returns:  A boolean value indicating that a valid session exists for
--             the given userid.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_session_expired(INT) RETURNS boolean AS'
DECLARE
   uid ALIAS FOR $1;
   curtime timestamp := ''NOW'';
   sess sessions%ROWTYPE;
BEGIN
   SELECT INTO sess * FROM sessions WHERE session_user_id=uid;
   IF NOT FOUND THEN
      --RAISE EXCEPTION ''No session for userid %'', uid;
      RETURN true;
   ELSE
      IF sess.session_time < curtime THEN
         RETURN true;
      ELSE
         RETURN false;
      END IF;
   END IF;
END;
' LANGUAGE plpgsql;

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_session_expired
--   Input:    The integer representing a users' ID.
--   Output:   None.
--   Returns:  A boolean value indicating that a valid session exists for
--             the given username.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_session_expired(TEXT) RETURNS boolean AS'
DECLARE
   sessid ALIAS FOR $1;
   curtime timestamp := ''NOW'';
   sess sessions%ROWTYPE;
BEGIN
   SELECT INTO sess * FROM sessions WHERE session_id=sessid;
   IF NOT FOUND THEN
      --RAISE EXCEPTION ''session id % does not exist'', sessid;
      RETURN true;
   ELSE
      IF sess.session_time < curtime THEN
         RETURN true;
      ELSE
         RETURN false;
      END IF;
   END IF;
END;
' LANGUAGE plpgsql;

