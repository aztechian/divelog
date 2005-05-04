/*
CREATE FUNCTION auto_increment_dives_pk() RETURNS TRIGGER AS '
  begin
  new.diveid := nextval("dives_pk_sequence");
  return new;
  end;
'
LANGUAGE plpgsql;
*/

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

CREATE OR REPLACE FUNCTION do_user_login(TEXT,TEXT) RETURNS TEXT AS'
DECLARE
   uname ALIAS FOR $1;
   ip ALIAS FOR $2;
   sessid text;
   uid int;
   curtime timestamp;
   exptime timestamp;
BEGIN
   curtime := ''NOW'';
   exptime := timestamp ''NOW'' + interval ''3 hours'';
   SELECT INTO uid userid FROM users WHERE username=uname;
   IF NOT FOUND THEN
      RAISE EXCEPTION ''user % not found'', uname;
   END IF;
   --check if session already exists for this user
   SELECT INTO sessid session_id FROM sessions WHERE session_user_id=uid;
   IF NOT FOUND THEN
      INSERT INTO sessions VALUES( md5(''uname''||curtime), uid, curtime, exptime, ip, true );
      UPDATE users SET loggedin=true WHERE userid=uid;
      SELECT INTO sessid session_id FROM sessions WHERE session_user_id=uid;
   ELSE
      UPDATE sessions SET session_time=(timestamp ''now'' + interval ''3 hours'');
   END IF;
   RETURN sessid;
END;
' LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION sel_dive_data(INT) RETURNS SETOF RECORD AS'
DECLARE
   uid ALIAS FOR $1;
   data record;
BEGIN
   FOR data in SELECT * FROM dives LOOP
      return next data;
   END LOOP;
   RETURN;
END;
' LANGUAGE plpgsql;

