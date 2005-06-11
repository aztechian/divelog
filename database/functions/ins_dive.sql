--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################


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

