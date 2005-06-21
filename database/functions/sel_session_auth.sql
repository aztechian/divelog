CREATE OR REPLACE FUNCTION sel_session_auth(TEXT) RETURNS boolean AS '
DECLARE
	sid ALIAS FOR $1;
	time timestamp := ''now'';
BEGIN
	PERFORM * FROM sessions WHERE session_id = sid AND session_logged_in = ''true'' AND session_time > time;
	IF FOUND THEN
		RETURN true;
	END IF;
	RETURN false;
END;
' LANGUAGE plpgsql;

