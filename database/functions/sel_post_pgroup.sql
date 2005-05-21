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

