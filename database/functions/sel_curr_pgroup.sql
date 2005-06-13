--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_curr_pgroup
--   Input:
--   Output:
--   Returns:
--
--/////////////////////////////////////////////////////////////////////////

CREATE OR REPLACE FUNCTION sel_curr_pgroup(INT,TIMESTAMP) RETURNS CHAR(1) AS '
DECLARE
   uid ALIAS FOR $1;
   searchtime ALIAS FOR $2;

   pg char(1) := '''';
   dive_time INTERVAL HOUR to MINUTE;
   surf_time INTERVAL HOUR to MINUTE := ''0:00'';
   previous_time_out TIMESTAMP := ''epoch'';
   has_dives boolean;
   dive_recs dives%ROWTYPE;
BEGIN
   SELECT INTO has_dives sel_has_previous_dives(uid,searchtime);
   IF has_dives = false THEN
      RETURN ''A'';
   END IF;

   FOR dive_recs IN SELECT * FROM dives WHERE userid = uid ORDER BY time_in LOOP
      -- Set some variables. We want the length of this dive, and the surface interval between the
      -- previous dive and this one.
      dive_time := date_trunc(''minute'',dive_recs.time_out) - date_trunc(''minute'',dive_recs.time_in);
      surf_time := date_trunc(''minute'',dive_recs.time_in) - date_trunc(''minute'',previous_time_out);
      
      IF pg <> '''' OR surf_time <= INTERVAL''6 Hour'' THEN
         --Update the pressure group for the surface interval between the previous dive and this dive
         SELECT INTO pg sel_surface_creds(pg,surf_time);
      END IF;
      --Update the pressure group again for the current dive
      SELECT INTO pg sel_post_pgroup(pg,dive_time,dive_recs.depth);
      IF pg = '''' OR pg IS NULL THEN
         --IF we get blank returned from sel_post_pgroup, then they "went off the deep end"...HA!
         RAISE EXCEPTION ''Dive with dive_id of % exceeded no decompresssion limit. Cannot compute pressure groups.'', dive_recs.diveid;
      END IF;
      --Set this for the next iteration.
      previous_time_out := dive_recs.time_out;
   END LOOP;
   surf_time := date_trunc(''minute'',searchtime) - date_trunc(''minute'',previous_time_out);
   SELECT INTO pg sel_surface_creds(pg,surf_time);
   RETURN pg;
END;
' LANGUAGE plpgsql;

