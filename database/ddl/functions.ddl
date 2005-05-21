--/////////////////////////////////////////////////////////////////////////
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--/////////////////////////////////////////////////////////////////////////
/*
CREATE FUNCTION auto_increment_dives_pk() RETURNS TRIGGER AS '
  begin
  new.diveid := nextval("dives_pk_sequence");
  return new;
  end;
'
LANGUAGE plpgsql;
*/
--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_user_exists
--   Input:    The username of the user to be determined it is valid
--   Output:   None.
--   Returns:  A boolean value indicating that the given name exists in the 
--             system.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_user_exists(TEXT) RETURNS boolean AS '
DECLARE
   name record;
BEGIN
   SELECT INTO name userid FROM users WHERE username=$1;
   IF NOT FOUND THEN
      RETURN false;
   END IF;
   RETURN true;
END;
' LANGUAGE plpgsql;

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_user_creds
--   Input:    The username of the user to return a password for
--   Output:   None.
--   Returns:  The md5 encrypted password of the user specified. This should
--             be compared to the md5 hash of the supplied password to check
--             for a match. In no cases will the function return a decrypted
--             password.
--
--/////////////////////////////////////////////////////////////////////////
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

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_dive_data
--   Input:    The integer representing a users' ID.
--   Output:   None.
--   Returns:  A set of records holding all of that users dive meta-data.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_dive_data(INT) RETURNS SETOF RECORD AS'
DECLARE
   uid ALIAS FOR $1;
   data record;
BEGIN
   FOR data in SELECT * FROM dives WHERE userid=uid ORDER BY time_in LOOP
      return next data;
   END LOOP;
   RETURN;
END;
' LANGUAGE plpgsql;

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

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: ins_user
--   Input:    The username to be created, the clear-text password to be
--             associated with the user, the users first name, the users
--             last name, the users email address, the users location, the
--             users timezone.
--   Output:   None.
--   Returns:  An integer representing the user ID of the newly created user.
--             Returns '0' (zero) if there was an error during row creation.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION ins_user(TEXT,TEXT,TEXT,TEXT,TEXT,TEXT,INT) RETURNS int AS '
DECLARE
   name ALIAS FOR $1;
   pass ALIAS FOR $2;
   fname ALIAS FOR $3;
   lname ALIAS FOR $4;
   email ALIAS FOR $5;
   loc ALIAS FOR $6;
   tz ALIAS FOR $7;

   uid int;
   curdate timestamp := ''now'';
BEGIN
   INSERT INTO users(username,password,user_fname,user_lname,user_email,location,timezone,regdate,postcount)
     VALUES(name,md5(pass),fname,lname,email,loc,tz,curdate,0);
   IF NOT FOUND THEN
      RETURN 0;
   END IF;
   SELECT INTO uid userid FROM users WHERE username=name;
   IF NOT FOUND THEN
      RETURN 0;
   END IF;
   RETURN uid;
END;
' LANGUAGE plpgsql;


--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: ins_dive
--   Input:    
--   Output:   
--   Returns:  
--
--/////////////////////////////////////////////////////////////////////////
/*CREATE OR REPLACE FUNCTION ins_dive(INT,TIMESTAMP,TIMESTAMP,INT,INT,INT,INT,INT,INT,TEXT,TEXT,TEXT,TEXT,TEXT,POINT,INTERVAL HOUR to MINUTE,INT) AS '
DECLARE
   uid          ALIAS FOR $1;
   start        ALIAS FOR $2;
   end          ALIAS FOR $3;
   depth        ALIAS FOR $4;  --END OF REQUIRED FIELDS
   surface_temp ALIAS FOR $5;
   vis          ALIAS FOR $6;
   weight       ALIAS FOR $7;
   winds        ALIAS FOR $8;
   waves        ALIAS FOR $9;
   comment      ALIAS FOR $10;
   desc         ALIAS FOR $11;
   city         ALIAS FOR $12;
   state        ALIAS FOR $13;
   country      ALIAS FOR $14;
   coords       ALIAS FOR $15;
   bt           ALIAS FOR $16;
   safety_stop  ALIAS FOR $17;

   dive_id int;
   newrec dives%ROWTYPE;
BEGIN
   INSERT INTO dives(userid,surface_temp,visability,weight,windspeed,waves,comments,description,location_city,location_state,location_country,location_coords,time_in,time_out,depth,bottom_time,safety_stop)
   VALUES( uid, null, surface_temp, vis, weight, windspeed, waves, comment, desc, city, state, country, coords, start, end, depth, bt, safety_stop );

   SELECT INTO dive_id currval('dives_diveid_seq');
   SELECT INTO newrec * FROM dives WHERE diveid=dive_id;
   IF newrec.bottom_time IS NULL THEN
      UPDATE dives SET bottom_time=(newrec.time_out - newrec.time_in)::INTERVAL HOUR to MINUTE WERE diveid=dive_id;
   END IF;
   IF newrec.press_group_in IS NULL THEN
      UPDATE dives SET press_group_in=sel_curr_pgroup(newrec.time_in) WHERE diveid=dive_id;
   END IF;
   IF newrec.press_group_out IS NULL THEN
      UPDATE dives SET press_group_out=sel_post_pgroup(newrec.time_out,depth,bt) WHERE diveid=dive_id;
   END IF;
   -- Incomplete. Finish doing checks and need functions for pressure groups. Set up return values.
   RETURN;
END;
' LANGUAGE plpgsql;
*/

CREATE OR REPLACE FUNCTION sel_post_pgroup(CHAR(1),INTERVAL HOUR to MINUTE, INT) RETURNS CHAR(1) AS '
DECLARE
   beg_pg ALIAS FOR $1;
   time ALIAS FOR $2;
   depth ALIAS FOR $3;

   tmp_bt interval hour to minute;
   end_pg char(1);
BEGIN
   IF beg_pg IS NULL THEN
      RETURN MIN(pressure_group) 
             FROM rdp1 
             WHERE rdp1.depth >= depth
             AND rdp1.time >= time;
   END IF;
   RETURN MIN(pressure_group)
          FROM rdp1 
          WHERE rdp1.depth >= depth
          AND rdp1.time >= time + (
            SELECT MAX(residual) FROM rdp3 WHERE pressure_group = beg_pg AND depth >= depth
          );
END;
' LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION sel_has_previous_dives(INT,TIMESTAMP) RETURNS boolean AS '
DECLARE
   uid ALIAS FOR $1;
   searchtime ALIAS FOR $2;
BEGIN
   SELECT diveid FROM dives WHERE userid=uid AND time_out > searchtime - INTERVAL'6 Hour';
   IF FOUND THEN
      RETURN true;
   ELSE
      RETURN false;
   END IF;
END;
' LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION sel_curr_pgroup(INT,TIMESTAMP) RETURNS CHAR(1) AS '
DECLARE
   uid ALIAS FOR $1;
   searchtime ALIAS FOR $2;
   
   has_dives boolean;
   dive_recs dives%ROWTYPE;
   dive_list record;
BEGIN
   SELECT INTO has_dives sel_has_previous_dives(uid,searchtime);
   IF NOT has_dives THEN
      RETURN 'A';
   END IF;


   FOR dive_recs IN SELECT * FROM dives WHERE userid=uid AND time_out > searchtime - INTERVAL'6 Hour' ORDER BY time_out ASC LOOP
      
