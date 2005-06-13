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
CREATE OR REPLACE FUNCTION ins_dive(INT,TIMESTAMP,TIMESTAMP,INT,INT,INT,INT,INT,INT,TEXT,TEXT,TEXT,TEXT,TEXT,POINT,INTERVAL HOUR to MINUTE,INT) RETURNS VOID AS '
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

   pg_in, pg_out CHAR(1);
   dive_id int;
   newrec dives%ROWTYPE;
BEGIN
   IF uid = '''' OR start = '''' OR end = '''' OR depth = '''' THEN
      RAISE EXCEPTION ''Required fields were not given for inserting a new dive'';
   END IF;

   IF bt IS NULL THEN
      bt := (end - start)::INTERVAL HOUR to MINUTE;
   END IF;
   
   pg_in := sel_curr_pgroup(uid,start);
   pg_out := sel_post_pgroup(pg_in,(end-start)::INTERVAL HOUR to MINUTE,depth);
   -- Incomplete. Finish doing checks and need functions for pressure groups. Set up return values.
   INSERT INTO dives(userid,surface_temp,visability,weight,windspeed,waves,comments,description,location_city,location_state,location_country,location_coords,time_in,time_out,press_group_in,press_group_out,depth,bottom_time,safety_stop)
   VALUES( uid, null, surface_temp, vis, weight, windspeed, waves, comment, desc, city, state, country, coords, start, end, pg_in, pg_out, depth, bt, safety_stop );
   
   RETURN;
END;
' LANGUAGE plpgsql;

