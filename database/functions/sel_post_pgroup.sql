--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_post_pgroup
--   Input:
--   Output:
--   Returns:
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_post_pgroup(CHAR(1),INTERVAL HOUR to MINUTE, INT) RETURNS CHAR(1) AS '
DECLARE
   beg_pg ALIAS FOR $1;
   time ALIAS FOR $2;
   depth ALIAS FOR $3;

   tmp_bt interval hour to minute;
   end_pg char(1) := '''';
BEGIN
   IF beg_pg = '''' THEN
      SELECT INTO end_pg MIN(pressure_group)
             FROM rdp1
             WHERE rdp1.depth >= depth
             AND rdp1.time >= time;
   ELSE
      SELECT INTO end_pg MIN(pressure_group)
         FROM rdp1
         WHERE rdp1.depth >= depth
         AND rdp1.time >= time + (
           SELECT sel_residual(beg_pg,depth)
         );
   END IF;
   /*This function should return a blank to indicate that the diver went over the no-deco
     limit, rather than just defaulting to a Z
   */
   --IF end_pg = '''' OR end_pg IS NULL THEN
   --   end_pg := ''Z'';
   --END IF;
   RETURN end_pg;
END;
' LANGUAGE plpgsql;

