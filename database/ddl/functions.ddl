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

CREATE OR REPLACE FUNCTION do_clear_sessions() RETURNS void AS'
DECLARE
   curtime timestamp := ''now'';
BEGIN 
   DELETE FROM sessions WHERE session_time < curtime;
   RETURN;
END;
' LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION do_user_logout(INT) RETURNS void AS'
DECLARE
   uid ALIAS FOR $1;
   uname text;
   curtime timestamp := ''now'';
BEGIN
   DELETE FROM sessions WHERE session_user_id=uid;
   UPDATE users SET loggedin=false, lastvisit=curtime WHERE userid=uid;
   RETURN;
END;
' LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION ins_session(TEXT) RETURNS void AS'
DECLARE
   curtime timestamp := ''now'';
   exptime timestamp := timestamp ''now'' + interval ''3 hours'';
BEGIN
   INSERT INTO sessions VALUES($1,null,curtime,exptime,null,false);
   RETURN;
END;
' LANGUAGE plpgsql;
