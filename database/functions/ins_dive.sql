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
   i_userid            ALIAS FOR $1;
   i_time_in           ALIAS FOR $2;
   i_time_out          ALIAS FOR $3;
   i_depth             ALIAS FOR $4;  --END OF REQUIRED FIELDS
   i_surface_temp      ALIAS FOR $5;
   i_visibility        ALIAS FOR $6;
   i_weight            ALIAS FOR $7;
   i_windspeed         ALIAS FOR $8;
   i_waves             ALIAS FOR $9;
   i_comments          ALIAS FOR $10;
   i_description       ALIAS FOR $11;
   i_location_city     ALIAS FOR $12;
   i_location_state    ALIAS FOR $13;
   i_location_country  ALIAS FOR $14;
   i_location_coords   ALIAS FOR $15;
   i_bottom_time       ALIAS FOR $16;
   i_safety_stop       ALIAS FOR $17;

   pg_in CHAR(1);
   pg_out CHAR(1);
   bt INTERVAL HOUR to MINUTE := INTERVAL ''0 Hour 0 Minute'';
   dive_num int := 0;
   newrec dives%ROWTYPE;
BEGIN
   IF i_userid IS NULL OR i_time_in IS NULL OR i_time_out IS NULL OR i_depth IS NULL THEN
      RAISE EXCEPTION ''Required fields were not given for inserting a new dive'';
   END IF;

   IF i_bottom_time = ''0:00'' OR i_bottom_time IS NULL THEN
      bt := (i_time_out - i_time_in)::INTERVAL HOUR to MINUTE;
   ELSE
      bt := i_bottom_time;
   END IF;
   
   SELECT INTO dive_num COUNT(*)+1 FROM dives WHERE userid = i_userid;
   pg_in := sel_curr_pgroup(i_userid,i_time_in);
   pg_out := sel_post_pgroup(pg_in,(i_time_out-i_time_in)::INTERVAL HOUR to MINUTE,i_depth);
   -- Incomplete. Finish doing checks and need functions for pressure groups. Set up return values.
   INSERT INTO dives(userid,
   					divenum,
   					surface_temp,
   					visability,
   					weight,
   					windspeed,
   					waves,
   					comments,
   					description,
   					location_city,
   					location_state,
   					location_country,
   					location_coords,
   					time_in,
   					time_out,
   					press_group_in,
   					press_group_out,
   					depth,
   					bottom_time,
   					safety_stop
   		)
   VALUES( i_userid,
   		dive_num,
		i_surface_temp,
		i_visibility,
		i_weight,
		i_windspeed,
		i_waves,
		i_comments,
		i_description,
		i_location_city,
		i_location_state,
		i_location_country,
		i_location_coords,
		i_time_in,
		i_time_out,
		pg_in,
		pg_out,
		i_depth,
		bt,
		i_safety_stop
	);
   
   RETURN true;
END;
' LANGUAGE plpgsql;

